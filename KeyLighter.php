<?php
/**
 * Highlighter
 *
 * Copyright (C) 2015, Some right reserved.
 *
 * @author Kacper "Kadet" Donat <kadet1090@gmail.com>
 *
 * Contact with author:
 * Xmpp: kadet@jid.pl
 * E-mail: kadet1090@gmail.com
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter;

use Kadet\Highlighter\Formatter\CliFormatter;
use Kadet\Highlighter\Formatter\FormatterInterface;
use Kadet\Highlighter\Formatter\HtmlFormatter;
use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Utils\Singleton;

/**
 * KeyLighter helper class, used to simplify usage.
 *
 * @package Kadet\Highlighter
 */
class KeyLighter
{
    use Singleton;

    const VERSION = '0.2.0';

    /**
     * Registered aliases
     *
     * @var string[]
     */
    private $_languages = [];

    /** @var FormatterInterface */
    private $_formatter = null;

    /**
     * @param string $name
     *
     * @return Language
     */
    public function getLanguage($name) {
        $embedded = [];
        if(($pos = strpos($name, '>')) !== false) {
            $embedded[] = self::getLanguage(trim(substr($name, $pos + 1)));
            $name       = trim(substr($name, 0, $pos));
        }

        $lang = isset($this->_languages[$name]) ? $this->_languages[$name] : 'Kadet\\Highlighter\\Language\\PlainText';
        return new $lang([
            'embedded' => $embedded
        ]);
    }

    /**
     * @param Language|callable|string $language
     * @param array[string]            $aliases
     */
    public function registerLanguage($language, $aliases) {
        $this->_languages = array_merge($this->_languages, array_fill_keys($aliases, $language));
    }

    public function setDefaultFormatter(FormatterInterface $formatter) {
        $this->_formatter = $formatter;
    }

    public function registeredLanguages() {
        return $this->_languages;
    }

    public function getDefaultFormatter() {
        return $this->_formatter;
    }

    public function highlight($source, $language, FormatterInterface $formatter = null) {
        $formatter = $formatter ?: $this->getDefaultFormatter();

        if(!$language instanceof Language) {
            $language = $this->getLanguage($language);
        }

        return $formatter->format($language->parse($source));
    }

    public function __construct() {
        $this->setDefaultFormatter(
            php_sapi_name() === 'cli' ?
                new CliFormatter() :
                new HtmlFormatter()
        );

        $this->registerLanguage('Kadet\\Highlighter\\Language\\PhpLanguage', ['php']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\XmlLanguage', ['xml', 'xaml']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\HtmlLanguage', ['html', 'htm']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\PowerShellLanguage', ['powershell', 'posh', 'ps1']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\PlainText', ['plaintext', 'text', 'none', 'txt']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\LatexLanguage', ['tex', 'latex']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\IniLanguage', ['ini', 'cfg']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\JavaScriptLanguage', ['js', 'jscript', 'javascript']);
    }
}