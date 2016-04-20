<?php
/**
 * Highlighter
 *
 * Copyright (C) 2016, Some right reserved.
 *
 * @author Kacper "Kadet" Donat <kacper@kadet.net>
 *
 * Contact with author:
 * Xmpp: me@kadet.net
 * E-mail: contact@kadet.net
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter;

use Kadet\Highlighter\Formatter\CliFormatter;
use Kadet\Highlighter\Formatter\FormatterInterface;
use Kadet\Highlighter\Formatter\HtmlFormatter;
use Kadet\Highlighter\Language\GreedyLanguage;
use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Language\PlainText;
use Kadet\Highlighter\Utils\Singleton;

/**
 * KeyLighter helper class, used to simplify usage.
 *
 * @package Kadet\Highlighter
 */
class KeyLighter
{
    use Singleton;

    const VERSION = '0.8.0-dev';

    /**
     * Registered aliases
     *
     * @var array
     */
    private $_languages = [
        'name'      => [],
        'mime'      => [],
        'extension' => []
    ];

    /** @var FormatterInterface */
    private $_formatter = null;

    /**
     * @param string $name
     *
     * @return Language
     */
    public function getLanguage($name)
    {
        $embedded = [];
        if (($pos = strpos($name, '>')) !== false) {
            $embedded[] = self::getLanguage(trim(substr($name, $pos + 1)));
            $name       = trim(substr($name, 0, $pos));
        }

        return $this->languageByName($name, [
            'embedded' => $embedded
        ]);
    }

    public function languageByName($name, $params = [])
    {
        return isset($this->_languages['name'][$name]) ?
            $this->_languages['name'][$name]($params) :
            new PlainText($params);
    }

    public function languageByMime($mime, $params = [])
    {
        return isset($this->_languages['mime'][$mime]) ?
            $this->_languages['mime'][$mime]($params) :
            new PlainText($params);
    }

    public function languageByExt($filename, $params = [])
    {
        foreach($this->_languages['extension'] as $mask => $class) {
            if(fnmatch($mask, $filename)) {
                return $class($params);
            }
        }

        return new PlainText($params);
    }

    /**
     * @param callable|string $language
     * @param array[string]   $aliases
     *
     * @deprecated Will be removed in 1.0
     */
    public function registerLanguage($language, $aliases)
    {
        $this->register($language, ['name' => $aliases]);
    }

    public function setDefaultFormatter(FormatterInterface $formatter)
    {
        $this->_formatter = $formatter;
    }

    public function registeredLanguages($by = 'name')
    {
        return array_map(function ($e) {
            return $e([])->getFQN();
        }, $this->_languages[$by]);
    }

    public function getDefaultFormatter()
    {
        return $this->_formatter;
    }

    public function highlight($source, Language $language, FormatterInterface $formatter = null)
    {
        $formatter = $formatter ?: $this->getDefaultFormatter();
        return $formatter->format($language->parse($source));
    }

    public function __construct()
    {
        $this->setDefaultFormatter(
            php_sapi_name() === 'cli' ? new CliFormatter() : new HtmlFormatter()
        );
    }

    public function init()
    {
        foreach(include __DIR__.'/Config/aliases.php' as $alias) {
            $class = $alias[0];
            unset($alias[0]);

            $this->register($class, $alias);
        }
    }

    /**
     * @param string|callable $class
     * @param array           $options
     */
    public function register($class, array $options)
    {
        if(!is_callable($class) && is_subclass_of($class, Language::class)) {
            $class = function($arguments = []) use ($class) {
                return new $class($arguments);
            };
        }

        foreach($options as $name => $values) {
            $this->_languages[$name] = array_merge($this->_languages[$name], array_fill_keys($values, $class));
        }
    }
}
