<?php
namespace Kadet\Xmpp;

use Kadet\SocketLib\Utils\Logger;
use Kadet\Utils\Event;
use Kadet\Utils\Property;
use Kadet\Utils\Timer;
use Kadet\Xmpp\Connector\AbstractConnector;
use Kadet\Xmpp\Connector\TcpConnector;
use Kadet\Xmpp\Roster\Roster;
use Kadet\Xmpp\Sasl\Mechanism;
use Kadet\Xmpp\Sasl\SaslFactory;
use Kadet\Xmpp\Stanza\Message;
use Kadet\Xmpp\Stanza\Presence;
use Kadet\Xmpp\Stanza\Stanza;
use Kadet\Xmpp\Xml\XmlBranch;

/**
 * XmppClient class provides basic XMPP/Jabber functionality.
 *
 * @package Kadet\Xmpp
 * @author  Kadet <kadet1090@gmail.com>
 *
 * @property-read bool $connected
 */
class XmppClient
{
    use Property;

    /**
     * Event fired when authorization process ends.
     *
     * @event(XmppClient $client, Stanza $result)
     * @var \Kadet\Utils\Event
     */
    public $onAuth;
    /**
     * Event fired when stream is opened and ready to accept data.
     *
     * @event(XmppClient $client)
     * @var \Kadet\Utils\Event
     */
    public $onStreamOpen;
    /**
     * Event fired when bot is ready (stream is opened, client is successfully authed and session is registered)
     *
     * @event(XmppClient $client)
     * @var \Kadet\Utils\Event
     */
    public $onReady;
    /**
     * Event fired on every loop tick.
     *
     * @event(XmppClient $client)
     * @var \Kadet\Utils\Event
     */
    public $onTick;
    /**
     * Event fired when presence packet came.
     *
     * @event(XmppClient $client, Presence $packet)
     * @var \Kadet\Utils\Event
     */
    public $onPresence;
    /**
     * Event fired when iq packet came.
     *
     * @event(XmppClient $client, Iq $packet)
     * @var \Kadet\Utils\Event
     */
    public $onIq;
    /**
     * Event fired when message packet came.
     *
     * @event(XmppClient $client, Message $packet)
     * @var \Kadet\Utils\Event
     */
    public $onMessage;
    /**
     * Event fired when user joins to room.
     *
     * @event(XmppClient $client, Room $room, User $user, bool $broadcast, Presence $presence)
     * @var \Kadet\Utils\Event
     */
    public $onJoin;
    /**
     * Event fired when user leaves room.
     *
     * @event(XmppClient $client, Room $room, User $user, Presence $presence)
     * @var \Kadet\Utils\Event
     */
    public $onLeave;
    /**
     * Event fired when client leaves room.
     *
     * @event(XmppClient $client, Room $room)
     * @var \Kadet\Utils\Event
     */
    public $onRoomLeave;
    /**
     * Event fired when client joins room.
     *
     * @event(XmppClient $client, Room $room)
     * @var \Kadet\Utils\Event
     */
    public $onRoomJoin;
    /**
     * Event fired when TLS connection is established.
     *
     * @event(XmppClient $client)
     * @var \Kadet\Utils\Event
     */
    public $onTls;
    /**
     * Event fired when new XMPP packet comes.
     *
     * @event(XmppClient $client, Stanza $result)
     * @var \Kadet\Utils\Event
     */
    public $onPacket;

    /**
     * Event fired when user on MUC changes nick
     *
     * @event(XmppClient $client, Room $room, User $user, Presence $presence, string $old, string $new)
     * @var \Kadet\Utils\Event
     */
    public $onNickChange;

    /**
     * Event fired when client loses connection to server.
     *
     * @event(XmppClient $client)
     * @var \Kadet\Utils\Event
     */
    public $onDisconnect;

    /**
     * Event fired when client established connection to server.
     *
     * @event(XmppClient $client)
     * @var \Kadet\Utils\Event
     */
    public $onConnect;

    /**
     * Logger of clients events.
     * @var Logger
     */
    public $logger;

    /**
     * Jabber account Jid
     *
     * @var Jid
     */
    public $jid;

    /**
     * If client is connected and authed is true.
     *
     * @var bool
     */
    public $isReady;

    /**
     * Rooms list.
     *
     * @var Room[]
     */
    public $rooms = array();

    public $roster;

    /**
     * Stream
     *
     * @var AbstractConnector
     */
    public $connector;

    /**
     * Password to jabber account.
     *
     * @var string
     */
    protected $password;
    /**
     * Mechanism used in sasl authentication
     *
     * @var Mechanism
     */
    protected $_mechanism;

    /**
     * @var array Array with expected stanzas.
     */
    private $_waiting = array();

    /**
     * @param Jid                         $jid      Clients JID
     * @param string                      $password Account Password
     * @param Connector\AbstractConnector $connector
     */
    public function __construct(Jid $jid, $password, AbstractConnector $connector = null)
    {
        if(!$connector)
            $connector = new TcpConnector($jid->server);

        $this->connector = $connector;
        $this->connector->client = $this;

        $this->jid      = $jid;
        $this->password = $password;

        $this->onPacket     = new Event();
        $this->onAuth       = new Event();
        $this->onStreamOpen = new Event();
        $this->onReady      = new Event();
        $this->onTick       = new Event();
        $this->onPresence   = new Event();
        $this->onMessage    = new Event();
        $this->onIq         = new Event();
        $this->onJoin       = new Event();
        $this->onLeave      = new Event();
        $this->onTls        = new Event();
        $this->onRoomJoin   = new Event();
        $this->onRoomLeave  = new Event();
        $this->onNickChange = new Event();
        $this->onConnect    = new Event();
        $this->onDisconnect = new Event();

        $this->roster = new Roster($this);

        $this->keepAliveTimer = new Timer(15, array($this, 'keepAliveTick'));
        $this->keepAliveTimer->stop(); // We don't want to run this before connection is finalized.

        $this->connector->onConnect->add(function($c) { $this->onConnect->run($this); });
        $this->connector->onDisconnect->add(function($c) { $this->onDisconnect->run($this); });

        $this->connector->onReceive->add([$this, 'connector_onReceive']);
        $this->connector->onFeatures->add([$this, 'connector_onFeatures']);

        $this->onConnect->add(array($this, '_onConnect'));
        $this->onAuth->add(array($this, '_onAuth'));
        $this->onReady->add(array($this, '_onReady'));
        $this->onPresence->add(array($this, '_onPresence'));
        $this->onMessage->add(array($this, '_onMessage'));
        $this->onTls->add(array($this, '_onTls'));
        $this->onPacket->add(array($this, '_onPacket'));
        $this->onPacket->add(array($this, '_handleExpected'));
        $this->onDisconnect->add(array($this, '_onDisconnect'));

        $settings = [
            'indent'           => true,
            'input-xml'        => true,
            'output-xml'       => true,
            'drop-empty-paras' => false,
            'wrap'             => 0
        ];

        $this->connector->onSend->add(function ($socket, $packet) use ($settings) {
            $len = strlen($packet);

            if (function_exists('tidy_repair_string'))
                $packet = trim(tidy_repair_string($packet, $settings));

            if (isset($this->logger))
                $this->logger->debug("Sent {length} bytes: \n{packet}", [
                    'length' => $len,
                    'packet' => $packet
                ]);
        });

        $this->connector->onReceive->add(function ($socket, $packet) use ($settings) {
            $len = strlen($packet);

            if (function_exists('tidy_repair_string'))
                $packet = trim(tidy_repair_string($packet, $settings));

            if (isset($this->logger))
                $this->logger->debug("Received {length} bytes: \n{packet}", [
                    'length' => $len,
                    'packet' => $packet
                ]);
        });

        XmlBranch::$bind['iq']       = 'Kadet\\Xmpp\\Stanza\\Iq';
        XmlBranch::$bind['presence'] = 'Kadet\\Xmpp\\Stanza\\Presence';
        XmlBranch::$bind['message']  = 'Kadet\\Xmpp\\Stanza\\Message';
        XmlBranch::$bind['query']    = 'Kadet\\Xmpp\\Stanza\\Iq\\Query';
    }

    /**
     * Should be private, but... php sucks!
     * DO NOT RUN IT, TRUST ME.
     *
     * @internal
     */
    public function _onConnect(XmppClient $client)
    {
        $this->connector->streamRestart($this->jid);
    }

    public function connector_onReceive(AbstractConnector $connector, $packet) {
        $this->onPacket->run($this, Stanza::fromXml($packet, $this));
    }

    public function write($packet)
    {
        $this->connector->send($packet instanceof XmlBranch ? $packet->asXml() : (string)$packet);
    }

    /**
     * @param string   $type
     * @param int      $id
     * @param callable $delegate
     */
    public function wait($type, $id, callable $delegate)
    {
        $this->_waiting[] = array(
            'tag'      => $type,
            'id'       => $id,
            'delegate' => $delegate
        );
    }

    /**
     * @param string $type
     * @param int    $id
     * @param int    $timeout Timeout in seconds
     *
     * @return Stanza
     */
    public function block($type, $id, $timeout = 10)
    {
        $start = time();
        $packet = null;

        $this->wait($type, $id, function ($pckt) use (&$packet) {
            $packet = $pckt;
        });

        while(!$packet && time() - $start < $timeout) {
            $this->process();
            usleep(5000);
        }

        return $packet;
    }

    /**
     * Should be private, but... php sucks!
     * DO NOT RUN IT, TRUST ME.
     *
     * @internal
     *
     * @param AbstractConnector $connector
     * @param XmlBranch         $features
     */
    public function connector_onFeatures(AbstractConnector $connector, XmlBranch $features)
    {
        if (isset($features->starttls)) {
            $this->startTls();

            return;
        }

        if (isset($features->mechanisms)) {
            $this->auth();

            return;
        }
    }

    private function startTls()
    {
        if ($this->logger) $this->logger->notice('Starting TLS negotiation.');

        $xml = new XmlBranch('starttls');
        $xml->addAttribute('xmlns', 'urn:ietf:params:xml:ns:xmpp-tls');
        $this->write($xml);
    }

    /**
     * Authorizes client on server using SASL
     *
     * @throws \RuntimeException
     */
    private function auth()
    {
        if ($this->logger)
            $this->logger->notice('SASL Auth, available mechanisms: {mechanisms}', [
                'mechanisms' => implode(', ', array_map(function (XmlBranch $item) {
                    return $item->content;
                }, (array)$this->connector->features->mechanisms[0]->mechanism))
            ]);

        $xml = new XmlBranch('auth');
        $xml->addAttribute('xmlns', 'urn:ietf:params:xml:ns:xmpp-sasl');

        $mechanism = null;
        $this->connector->features->mechanisms[0]->mechanism->getIterator();
        foreach ($this->connector->features->mechanisms[0]->mechanism as $current) {
            if ($mechanism = SaslFactory::get($current->content, $this->jid, $this->password))
                break;
        }

        if (!$mechanism)
            throw new \RuntimeException('This client is not supporting any of server auth mechanisms.');

        if ($this->logger)
            $this->logger->notice('Chosen mechanism: {mechanism}', ['mechanism' => $current->content]);

        $this->_mechanism = $mechanism;

        $xml->addAttribute('mechanism', $current->content);
        $xml->setContent($mechanism->auth());

        $this->write($xml);
    }

    /**
     * Should be private, but... php sucks!
     * DO NOT RUN IT, TRUST ME.
     *
     * @param XmppClient $client
     * @param Stanza     $result
     *
     * @throws \RuntimeException
     * @internal
     */
    public function _onAuth(XmppClient $client, Stanza $result)
    {
        if ($result->xml->getName() == 'success') {
            if ($this->logger)
                $this->logger->info('SASL Auth successful.');

            $this->connector->streamRestart($this->jid);
            $this->_bind();
        } elseif ($this->logger)
            $this->logger->error('SASL Auth failed, reason: {reason}', ['reason' => $result->text[0]]);
    }

    /**
     * Binds resource.
     *
     * @internal
     */
    private function _bind()
    {
        $xml = new XmlBranch('iq');
        $id  = uniqid('bind_');
        $xml->addAttribute('id', $id)
            ->addAttribute('type', 'set');

        $xml->addChild(new XmlBranch('bind'))
            ->addAttribute('xmlns', 'urn:ietf:params:xml:ns:xmpp-bind')
            ->addChild(new XmlBranch('resource'))
            ->setContent($this->jid->resource);

        $this->write($xml);
        $this->wait('iq', $id, array($this, '_bindResult'));
    }

    /**
     * Should be private, but... php sucks!
     * DO NOT RUN IT, TRUST ME.
     *
     * @param XmppClient $client
     * @param Stanza     $result
     *
     * @throws \RuntimeException
     * @internal
     */
    public function _onTls(XmppClient $client, Stanza $result)
    {
        if ($result->tag == 'proceed') {
            if ($this->logger)
                $this->logger->info('TLS Connection established.');

            $this->connector->startTls();
            $this->connector->streamRestart($this->jid);
        } else
            throw new \RuntimeException('Tls negotiation failed.');
    }

    /**
     * Resource binding result.
     *
     * @param $packet
     *
     * @throws \RuntimeException
     *
     * @internal
     */
    public function _bindResult(Stanza $packet)
    {
        if ($packet['type'] == 'result') {
            $iq = new xmlBranch("iq");
            $iq->addAttribute("type", "set");
            $iq->addAttribute("id", uniqid('sess_'));
            $iq->addChild(new xmlBranch("session"))->addAttribute("xmlns", "urn:ietf:params:xml:ns:xmpp-session");
            $this->write($iq->asXml());
            $this->isReady = true;
            $this->onReady->run($this);
        } else
            throw new \RuntimeException('Resource binding error.');
    }

    /**
     * Should be private, but... php sucks!
     * DO NOT RUN IT, TRUST ME.
     *
     * @ignore
     *
     * @param XmppClient $client
     */
    public function _onReady(XmppClient $client)
    {
        $iq = new xmlBranch("iq");
        $iq->addAttribute("type", "get");
        $iq->addAttribute("id", uniqid('roster_'));
        $iq->addChild(new xmlBranch("query"))->addAttribute("xmlns", "jabber:iq:roster");
        $this->write($iq);

        $this->presence();

        $this->keepAliveTimer->start();
    }

    /**
     * Changes client status on server.
     *
     * @param string $show   New show status for client, one of these:
     *                       chat, available, away, xa, dnd, unavailable.
     *                       (default available)
     * @param string $status Additional text status.
     */
    public function presence($show = "available", $status = "")
    {
        $xml = new xmlBranch("presence");
        $xml->addAttribute("from", $this->jid->__toString())
            ->addAttribute("id", uniqid());
        $xml->addChild(new xmlBranch("show"))->setContent($show);
        $xml->addChild(new xmlBranch("status"))->setContent($status);
        $xml->addChild(new xmlBranch("priority"))->setContent(50);

        $this->write($xml->asXml());
    }

    /**
     * Should be private, but... php sucks!
     * DO NOT RUN IT, TRUST ME.
     *
     * @internal
     */
    public function keepAliveTick()
    {
        $xml = new xmlBranch("iq");
        $xml->addAttribute("from", $this->jid->__toString());
        $xml->addAttribute("to", $this->jid->server);
        $xml->addAttribute("id", uniqid('ping_'));
        $xml->addAttribute("type", "get");
        $xml->addChild(new xmlBranch("ping"))->addAttribute("xmlns", "urn:xmpp:ping");

        $this->write($xml->asXml());
    }

    /**
     * Should be private, but... php sucks!
     * DO NOT RUN IT, TRUST ME.
     *
     * @param \Kadet\Xmpp\XmppClient    $client
     * @param \Kadet\Xmpp\Stanza\Stanza $stanza
     *
     * @internal
     */
    public function _onPacket(XmppClient $client, Stanza $stanza)
    {
        switch ($stanza->tag) {
            case 'presence':
                $this->onPresence->run($this, $stanza);
                break;
            case 'iq':
                $this->onIq->run($this, $stanza);
                break;
            case 'message':
                $this->onMessage->run($this, $stanza);
                break;

            # SASL
            case 'success':
            case 'failure':
            case 'proceed':
                if ($stanza['xmlns'] == 'urn:ietf:params:xml:ns:xmpp-sasl')
                    $this->onAuth->run($this, $stanza);
                elseif ($stanza['xmlns'] == 'urn:ietf:params:xml:ns:xmpp-tls')
                    $this->onTls->run($this, $stanza);

                break;
            case 'challenge':
                $this->_onChallenge($stanza);
                break;
        }
    }

    /**
     * Should be private, but... php sucks!
     * DO NOT RUN IT, TRUST ME.
     *
     * @param Stanza $packet
     *
     * @internal
     */
    public function _onChallenge($packet)
    {
        $xml = new XmlBranch('response');
        $xml->addAttribute('xmlns', 'urn:ietf:params:xml:ns:xmpp-sasl');
        $xml->setContent(base64_encode($this->_mechanism->challenge($packet)));
        $this->write($xml->asXML());
    }

    /**
     * Should be private, but... php sucks!
     * DO NOT RUN IT, TRUST ME.
     *
     * @param XmppClient $client
     * @param Presence   $packet
     *
     * @internal
     */
    public function _onPresence(XmppClient $client, Presence $packet)
    {
        $channelJid = $packet->from->bare();
        $jid        = new Jid($channelJid);

        if (!$jid->isChannel()) return;

        if ($packet->type != 'unavailable') {
            if (isset($this->rooms[$channelJid]->users[$packet->from->resource])) {
                $this->rooms[$channelJid]->users[$packet->from->resource]->presence = $packet;
            } else {
                $user = $this->rooms[$channelJid]->addUser(User::fromPresence($packet, $this));
                if (
                    (string)$user->jid == (string)$this->jid ||
                    $this->rooms[$channelJid]->nick == $packet->from->resource
                ) $user->self = true;

                $this->onJoin->run(
                    $this,
                    $this->rooms[$channelJid],
                    $user,
                    $this->rooms[$channelJid]->subject === false, // broadcast
                    $packet
                );

            }
        } elseif (isset($this->rooms[$channelJid])) {
            $item = $packet->xpath('//user:item', ['user' => 'http://jabber.org/protocol/muc#user']);

            if (!isset($item[0])) return;
            $item = $item[0];

            // Nickname change
            if ($packet->xpath('//user:status[@code="303"]', ['user' => 'http://jabber.org/protocol/muc#user'])) {
                $nick = [
                    'old' => $packet->from->resource,
                    'new' => $item['nick']
                ];

                $user = $this->rooms[$channelJid]->users[$nick['new']] = $this->rooms[$channelJid]->users[$nick['old']];
                unset($this->rooms[$channelJid]->users[$nick['old']]);
                $user->nick = $nick['new'];

                $this->onNickChange->run(
                    $this,
                    $this->rooms[$channelJid],
                    $user,
                    $packet,
                    $nick['old'],
                    $nick['new']
                );
            } else {
                $user = $this->rooms[$channelJid]->users[$packet->from->resource];
                $this->onLeave->run($this, $this->rooms[$channelJid], $user, $packet);
                $this->rooms[$channelJid]->removeUser($user);
            }
        }
    }

    /**
     * Should be private, but... php sucks!
     * DO NOT RUN IT, TRUST ME.
     *
     * @param XmppClient $client
     * @param Message    $packet
     *
     * @internal
     */
    public function _onMessage(XmppClient $client, Message $packet)
    {
        if ($packet->type != 'groupchat' || !isset($this->rooms[$packet->from->bare()])) return;

        if (isset($packet->subject))
            $this->rooms[$packet->from->bare()]->subject = $packet->subject;

        if (!isset($packet->delay) && $this->rooms[$packet->from->bare()]->subject === false)
            $this->rooms[$packet->from->bare()]->subject = ''; // Some strange workaround, servers doesn't meet specification... ;(
    }

    /**
     * Handles new packets
     */
    public function process()
    {
        if ($this->isReady)
            $this->onTick->run($this);

        Timer::update();
        $this->connector->read();
    }

    /**
     * Connects client to the server.
     */
    public function connect()
    {
        return $this->connector->connect();
    }

    /**
     * Gets user data by given jid.
     *
     * @param Jid $user Users jid
     *
     * @return User|null Users data.
     */
    public function getUserByJid(Jid $user)
    {
        if (!$user->fromChannel()) return null;

        return isset($this->rooms[$user->bare()]->users[$user->resource]) ?
            $this->rooms[$user->bare()]->users[$user->resource] :
            null;
    }

    /**
     * Sends message to specified jid.
     *
     * You could use it to send message to groupchat, but it is highly not recommended.
     *
     * @param Jid    $jid     Receiver jid
     * @param string $message Message content
     * @param string $type    Message type: char or groupchat.
     */
    public function message(Jid $jid, $message, $type = 'chat')
    {
        $msg = new XmlBranch('message');
        $msg->addAttribute('from', $this->jid->__toString())
            ->addAttribute('to', $jid->__toString())
            ->addAttribute('type', $type);
        $msg->addChild(new XmlBranch('body'))->setContent($message);
        $this->write($msg->asXml());
    }

    /**
     * Checks client version.
     *
     * @param Jid      $jid      Users jid.
     * @param callable $delegate Delegate to be executed after proper packet came.
     *                           Delegate takes one argument (packet) of type SimpleXMLElement.
     */
    public function version(Jid $jid, callable $delegate)
    {
        $id  = uniqid('osversion_');
        $xml = new xmlBranch("iq");
        $xml->addAttribute("from", $this->jid)
            ->addAttribute("to", $jid)
            ->addAttribute("type", "get")
            ->addAttribute("id", $id);

        $xml->addChild(new xmlBranch("query"))->addAttribute("xmlns", "jabber:iq:version");
        $this->write($xml->asXml());

        $this->wait('iq', $id, $delegate);
    }

    /**
     * Pings user.
     *
     * @param Jid      $jid      User jid.
     * @param callable $delegate Delegate to be executed after proper packet came.
     *                           Delegate takes one argument (packet) of type SimpleXMLElement.
     */
    public function ping(Jid $jid, callable $delegate)
    {
        $id  = uniqid('ping_');
        $xml = new xmlBranch("iq");
        $xml->addAttribute("from", $this->jid)
            ->addAttribute("to", $jid)
            ->addAttribute("type", "get")
            ->addAttribute("id", $id);

        $xml->addChild(new xmlBranch("ping"))->addAttribute("xmlns", "urn:xmpp:ping");
        $this->write($xml->asXml());

        $this->wait('iq', $id, $delegate);
    }

    /**
     * Joins to the room.
     *
     * @param Jid    $room Room to join, full jid.
     * @param string $nick Nick on room.
     *
     * @return Room Room data.
     * @throws \InvalidArgumentException
     */
    public function join(Jid $room, $nick)
    {
        if (!$room->isChannel()) throw new \InvalidArgumentException('room'); // YOU SHALL NOT PASS

        $xml = new xmlBranch("presence");
        $xml->addAttribute("from", $this->jid->__toString())
            ->addAttribute("to", $room->bare() . '/' . $nick)
            ->addAttribute("id", uniqid('mucjoin_'));
        $xml->addChild(new xmlBranch("x"))->addAttribute("xmlns", "http://jabber.org/protocol/muc");
        $this->write($xml->asXml());

        $this->rooms[$room->__toString()] = new Room($this, $room, $nick);
        $this->onRoomJoin->run($this, $this->rooms[$room->__toString()]);

        return $this->rooms[$room->__toString()];
    }

    /**
     * Leaves room.
     *
     * @param Jid $room Jid of room to leave.
     *
     * @internal Plugins should use Room::() instead of that.
     *
     * @throws \InvalidArgumentException
     */
    public function leave(Jid $room)
    {
        if (!$room->isChannel() || !isset($this->rooms[$room->bare()])) throw new \InvalidArgumentException('room');

        $xml = new xmlBranch("presence");
        $xml->addAttribute("from", $this->jid->__toString())
            ->addAttribute("to", $room->bare())
            ->addAttribute("id", uniqid('mucout_'))
            ->addAttribute("type", 'unavailable');
        $xml->addChild(new xmlBranch("x"))->addAttribute("xmlns", "http://jabber.org/protocol/muc");
        $this->write($xml->asXml());

        $this->onRoomLeave->run($this, $this->rooms[$room->bare()]);

        unset($this->rooms[$room->bare()]);
    }

    /**
     * Changes user role on room.
     *
     * @param Jid    $room   Jid of room.
     * @param string $nick   Nick of user.
     * @param string $role   New users role.
     *                       visitor, none, participant or moderator.
     * @param string $reason Reason of changing role. (default empty)
     *
     * @internal Plugins should use Room::role() instead of that.
     *
     * @throws \InvalidArgumentException
     */
    public function role(Jid $room, $nick, $role, $reason = '')
    {
        if (!in_array($role, array('visitor', 'none', 'participant', 'moderator')))
            throw new \InvalidArgumentException('role');

        $xml = new xmlBranch("iq");
        $xml->addAttribute("type", "set")
            ->addAttribute("to", $room->__toString())
            ->addAttribute("id", uniqid('role_'));

        $xml->addChild(new xmlBranch("query"));
        $xml->query[0]->addAttribute("xmlns", "http://jabber.org/protocol/muc#admin");
        $xml->query[0]->addChild(new xmlBranch("item"));
        $xml->query[0]->item[0]->addAttribute("nick", $nick);
        $xml->query[0]->item[0]->addAttribute("role", $role);

        if (!empty($reason)) $xml->query[0]->item[0]->addChild(new xmlBranch("reason"))->setContent($reason);

        $this->write($xml->asXml());
    }

    /**
     * Changes user affiliation.
     *
     * @param Jid    $room        Jid of room.
     * @param Jid    $user        Users Jid.
     * @param string $affiliation New affiliation for user.
     *                            none, outcast, member, admin, owner
     * @param string $reason      Reason of changing user affiliation.
     *
     * @internal Plugins should use Room::affiliation() instead of that.
     *
     * @throws \InvalidArgumentException
     */
    public function affiliate(Jid $room, Jid $user, $affiliation, $reason = '')
    {
        if (!in_array($affiliation, array('none', 'outcast', 'member', 'admin', 'owner')))
            throw new \InvalidArgumentException('affiliation');

        $xml = new xmlBranch("iq");
        $xml->addAttribute("type", "set")
            ->addAttribute("to", $room->__toString())
            ->addAttribute("id", uniqid('affiliate_'));

        $xml->addChild(new xmlBranch("query"));
        $xml->query[0]->addAttribute("xmlns", "http://jabber.org/protocol/muc#admin");
        $xml->query[0]->addChild(new xmlBranch("item"));
        $xml->query[0]->item[0]->addAttribute("jid", $user->bare());
        $xml->query[0]->item[0]->addAttribute("affiliation", $affiliation);

        if (!empty($reason)) $xml->query[0]->item[0]->addChild(new xmlBranch("reason"))->setContent($reason);

        $this->write($xml->asXml());
    }

    /**
     * Sets room (or conversation) subject.
     *
     * @param Jid    $jid     Jid to send subject msg.
     * @param string $subject New subject.
     *
     * @internal Plugins should use Room::subject() instead of that.
     */
    public function setSubject(Jid $jid, $subject)
    {
        $msg = new XmlBranch('message');
        $msg->addAttribute('from', $this->jid->__toString())
            ->addAttribute('to', $jid->__toString())
            ->addAttribute('type', $jid->isChannel() ? 'groupchat' : 'chat');
        $msg->addChild(new XmlBranch('subject'))->setContent($subject);
        $this->write($msg->asXml());
    }

    /**
     * Gets user affiliation list.
     *
     * @param Jid      $room        Jid of room to query.
     * @param string   $affiliation Affiliation type.
     * @param callable $delegate    Delegate to run after proper packet came.
     *                              Delegate takes one argument (packet) of type SimpleXMLElement.
     *
     * @internal Plugins should use Room::affiliationList() instead of that.
     *
     * @throws \InvalidArgumentException
     */
    public function affiliationList(Jid $room, $affiliation, callable $delegate)
    {
        if (!in_array($affiliation, array('none', 'outcast', 'member', 'admin', 'owner')))
            throw new \InvalidArgumentException('affiliation');

        $xml = new xmlBranch("iq");
        $id  = uniqid('affiliate_');
        $xml->addAttribute("type", "get")
            ->addAttribute('from', $this->jid->__toString())
            ->addAttribute("to", $room->__toString())
            ->addAttribute("id", $id);
        $xml->addChild(new xmlBranch("query"));
        $xml->query[0]->addAttribute("xmlns", "http://jabber.org/protocol/muc#admin");
        $xml->query[0]->addChild(new xmlBranch("item"));
        $xml->query[0]->item[0]->addAttribute("affiliation", $affiliation);
        $this->write($xml->asXml());

        $this->wait('iq', $id, $delegate);
    }

    /**
     * @param \Kadet\Xmpp\XmppClient    $client
     * @param \Kadet\Xmpp\Stanza\Stanza $packet
     *
     * @internal
     */
    public function _handleExpected(XmppClient $client, Stanza $packet)
    {
        foreach ($this->_waiting as &$wait) {
            if (
                (empty($wait['tag']) || $packet->tag == $wait['tag']) &&
                (empty($wait['id']) || $packet->id == $wait['id'])
            ) {
                $wait['delegate']($packet);
            }
        }
    }

    /**
     * @internal
     */
    public function _onDisconnect()
    {
        $this->logger->info('Disconnected.');
    }

    public function disconnect() {
        $this->connector->disconnect();
    }

    public function __destruct() {
        if($this->connector->connected)
            $this->disconnect();
    }
}