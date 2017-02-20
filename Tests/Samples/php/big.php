<?php
/**
 * Nucleus - XMPP Library for PHP
 *
 * Copyright (C) 2016, Some rights reserved.
 *
 * @author Kacper "Kadet" Donat <kacper@kadet.net>
 *
 * Contact with author:
 * Xmpp: me@kadet.net
 * E-mail: contact@kadet.net
 *
 * From Kadet with love.
 */

namespace Kadet\Xmpp;

use DI\Container;
use DI\ContainerBuilder;
use Interop\Container\ContainerInterface;
use Kadet\Xmpp\Component\Roster;
use Kadet\Xmpp\Exception\InvalidArgumentException;
use Kadet\Xmpp\Exception\WriteOnlyException;
use Kadet\Xmpp\Component\Authenticator;
use Kadet\Xmpp\Component\Binding;
use Kadet\Xmpp\Component\Component;
use Kadet\Xmpp\Component\ComponentInterface;
use Kadet\Xmpp\Component\SaslAuthenticator;
use Kadet\Xmpp\Component\TlsEnabler;
use Kadet\Xmpp\Network\Connector;
use Kadet\Xmpp\Stanza\Stanza;
use Kadet\Xmpp\Stream\Features;
use Kadet\Xmpp\Utils\Accessors;
use Kadet\Xmpp\Utils\filter as with;
use Kadet\Xmpp\Utils\ServiceManager;
use Kadet\Xmpp\Xml\XmlElementFactory;
use Kadet\Xmpp\Xml\XmlParser;
use Kadet\Xmpp\Xml\XmlStream;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\ExtendedPromiseInterface;

/**
 * Class XmppClient
 * @package Kadet\Xmpp
 *
 * @property Features           $features  Features provided by that stream
 * @property XmlParser          $parser    XmlParser instance used to process data from stream, can be exchanged only
 *                                         when client is not connected to server.
 * @property string             $resource  Client's jid resource
 * @property Jid                $jid       Client's jid (Jabber Identifier) address.
 * @property ContainerInterface $container Dependency container used for module management.
 * @property string             $language  Stream language (reflects xml:language attribute)
 * @property string             $state     Current client state
 *                                         `disconnected`   - not connected to any server,
 *                                         `connected`      - connected to server, but nothing happened yet,
 *                                         `secured`        - [optional] TLS negotiation succeeded, after stream restart
 *                                         `authenticated`  - authentication succeeded,
 *                                         `bound`          - resource binding succeeded,
 *                                         `ready`          - client is ready to operate
 *
 *                                         However modules can add custom states.
 * @property-read Roster             $roster    Clients roster.
 *
 * @property Connector    $connector Connector used for obtaining stream
 * @property-write string       $password  Password used for client authentication
 *
 * @event stanza(Stanza $stanza)       Emitted on every incoming stanza regardless of it's kind.
 *                                     Equivalent of element event with only instances of Stanza class allowed.
 * @event iq(Iq $iq)                   Emitted on every incoming iq stanza.
 * @event message(Message $message)    Emitted on every incoming message stanza.
 * @event presence(Presence $presence) Emitted on every incoming presence stanza.
 *
 * @event init(ArrayObject $queue)     Emitted when connection is accomplished, after binding process.
 *
 * @event state(string $state)         Emitted on state change.
 * @event bind(Jid $jid)               Emitted after successful bind.
 */
class XmppClient extends XmlStream implements ContainerInterface
{
    use ServiceManager, Accessors;

    /**
     * Connector used to instantiate stream connection to server.
     *
     * @var Connector
     */
    private $_connector;

    /**
     * Client's jid (Jabber Identifier) address.
     *
     * @var Jid
     */
    private $_jid;

    /**
     * Dependency container used as service manager.
     *
     * @var Container
     */
    private $_container;

    /**
     * Features provided by that stream
     *
     * @var Features
     */
    private $_features;

    /**
     * Current client state.
     *
     * @var string
     */
    private $_state = 'disconnected';
    private $_lang;

    /**
     * XmppClient constructor.
     * @param Jid                  $jid
     * @param array                $options {
     *     @var XmlParser          $parser          Parser used for interpreting streams.
     *     @var Component[]        $modules         Additional modules registered when creating client.
     *     @var string             $language        Stream language (reflects xml:language attribute)
     *     @var ContainerInterface $container       Dependency container used for module management.
     *     @var bool               $default-modules Load default modules or not
     * }
     */
    public function __construct(Jid $jid, array $options = [])
    {
        $container = new ContainerBuilder();
        $container->useAutowiring(false);

        $options = array_replace([
            'parser'    => new XmlParser(new XmlElementFactory()),
            'language'  => 'en',
            'container' => $container->build(),
            'connector' => $options['connector'] ?? new Connector\TcpXmppConnector($jid->domain, $options['loop']),
            'jid'       => $jid,

            'modules'         => [],
            'default-modules' => true,
        ], $options);

        parent::__construct($options['parser'], null);
        unset($options['parser']);

        $this->applyOptions($options);

        $this->on('element', function (Features $element) {
            $this->_features = $element;
            $this->emit('features', [$element]);
        }, Features::class);

        $this->on('element', function (Stanza $stanza) {
            $this->emit('stanza', [ $stanza ]);
            $this->emit($stanza->localName, [ $stanza ]);
        }, Stanza::class);

        $this->on('close', function () {
            $this->state = 'disconnected';
        });
    }

    public function applyOptions(array $options)
    {
        $options = \Kadet\Xmpp\Utils\helper\rearrange($options, [
            'container' => 6,
            'jid'       => 5,
            'connector' => 4,
            'modules'   => 3,
            'password'  => -1
        ]);

        if ($options['default-modules']) {
            $options['modules'] = array_merge([
                TlsEnabler::class    => new TlsEnabler(),
                Binding::class       => new Binding(),
                Authenticator::class => new SaslAuthenticator(),
                Roster::class        => new Roster()
            ], $options['modules']);
        }

        foreach ($options as $name => $value) {
            $this->$name = $value;
        }
    }

    public function start(array $attributes = [])
    {
        parent::start(array_merge([
            'xmlns'    => 'jabber:client',
            'version'  => '1.0',
            'language' => $this->_lang
        ], $attributes));
    }

    public function connect()
    {
        $this->getLogger()->debug("Connecting to {$this->_jid->domain}");

        $this->_connector->connect();
    }

    public function bind($jid)
    {
        $this->jid = new Jid($jid);
        $this->emit('bind', [$jid]);

        $this->state = 'bound';

        $queue = new \SplQueue();
        $this->emit('init', [ $queue ]);

        \React\Promise\all(iterator_to_array($queue))->then(function() {
            $this->state = 'ready';
        });
    }

    /**
     * Registers module in client's dependency container.
     *
     * @param ComponentInterface $module    Module to be registered
     * @param bool|string|array  $alias     Module alias, class name by default.
     *                                      `true` for aliasing interfaces and parents too,
     *                                      `false` for aliasing as class name only
     *                                      array for multiple aliases,
     *                                      and any string for alias name
     */
    public function register(ComponentInterface $module, $alias = true)
    {
        $module->setClient($this);
        $this->_container->set(get_class($module), $module);

        if ($alias === true) {
            $this->_addToContainer($module, array_merge(class_implements($module), array_slice(class_parents($module), 1)));
        } elseif(is_array($alias)) {
            $this->_addToContainer($module, $alias);
        } else {
            $this->_addToContainer($module, [ $alias === false ? get_class($module) : $alias ]);
        }
    }

    private function _addToContainer(ComponentInterface $module, array $aliases) {
        foreach ($aliases as $name) {
            if (!$this->has($name)) {
                $this->_container->set($name, $module);
            }
        }
    }

    /**
     * Sends stanza to server and returns promise with server response.
     *
     * @param Stanza $stanza
     * @return ExtendedPromiseInterface
     */
    public function send(Stanza $stanza) : ExtendedPromiseInterface
    {
        $deferred = new Deferred();

        $this->once('element', function(Stanza $stanza) use ($deferred) {
            if($stanza->type === "error") {
                $deferred->reject($stanza);
            } else {
                $deferred->resolve($stanza);
            }
        }, with\stanza\id($stanza->id));
        $this->write($stanza);

        return $deferred->promise();
    }

    private function handleConnect($stream)
    {
        $this->exchangeStream($stream);

        $this->getLogger()->info("Connected to {$this->_jid->domain}");
        $this->start([
            'from' => (string)$this->_jid,
            'to'   => $this->_jid->domain
        ]);

        $this->state = 'connected';

        return $this->emit('connect');
    }

    //region Features
    public function getFeatures()
    {
        return $this->_features;
    }
    //endregion

    //region Parser
    public function setParser(XmlParser $parser)
    {
        if($this->state !== "disconnected") {
            throw new \BadMethodCallException('Parser can be changed only when client is disconnected.');
        }

        parent::setParser($parser);
        $this->_parser->factory->load(require __DIR__ . '/XmlElementLookup.php');
    }

    public function getParser()
    {
        return $this->_parser;
    }
    //endregion

    //region Connector
    protected function setConnector($connector)
    {
        if ($connector instanceof LoopInterface) {
            $this->_connector = new Connector\TcpXmppConnector($this->_jid->domain, $connector);
        } elseif ($connector instanceof Connector) {
            $this->_connector = $connector;
        } else {
            throw new InvalidArgumentException(sprintf(
                '$connector must be either %s or %s instance, %s given.',
                LoopInterface::class, Connector::class, \Kadet\Xmpp\Utils\helper\typeof($connector)
            ));
        }

        $this->_connector->on('connect', function ($stream) {
            return $this->handleConnect($stream);
        });
    }

    public function getConnector()
    {
        return $this->_connector;
    }
    //endregion

    //region Resource
    public function setResource(string $resource)
    {
        $this->_jid = new Jid($this->_jid->domain, $this->_jid->local, $resource);
    }

    public function getResource()
    {
        return $this->_jid->resource;
    }
    //endregion

    //region Password
    public function setPassword(string $password)
    {
        $this->get(Authenticator::class)->setPassword($password);
    }

    public function getPassword()
    {
        throw new WriteOnlyException("Password can't be obtained.");
    }
    //endregion

    //region Modules
    public function setModules(array $modules)
    {
        foreach ($modules as $name => $module) {
            $this->register($module, is_string($name) ? $name : true);
        }
    }
    //endregion

    //region State
    public function setState($state)
    {
        $this->_state = $state;
        $this->emit('state', [$state]);
    }

    public function getState()
    {
        return $this->_state;
    }
    //endregion

    //region Container
    protected function getContainer() : ContainerInterface
    {
        return $this->_container;
    }

    protected function setContainer(Container $container)
    {
        $this->_container = $container;
    }
    //endregion

    //region Language
    public function getLanguage(): string
    {
        return $this->_lang;
    }

    public function setLanguage(string $language)
    {
        $this->_lang = $language;
    }
    //endregion

    //region JID
    public function getJid()
    {
        return $this->_jid;
    }

    protected function setJid(Jid $jid)
    {
        $this->_jid = $jid;
    }
    //endregion

    //region Roster
    /**
     * @return Roster
     */
    public function getRoster(): Roster
    {
        return $this->get(Roster::class);
    }
    //endregion
}