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

class KeyLighter
{
    const VERSION = '0.2.0';

    /** @var */
    private static $_languages = [];
    /** @var FormatterInterface */
    private static $_formatter = null;

    /**
     * @param string $name
     *
     * @return Language
     */
    public static function getLanguage($name) {
        $embedded = [];
        if(($pos = strpos($name, '>')) !== false) {
            $embedded[] = self::getLanguage(trim(substr($name, $pos + 1)));
            $name       = trim(substr($name, 0, $pos));
        }

        $lang = isset(self::$_languages[$name]) ? self::$_languages[$name] : 'Kadet\\Highlighter\\Language\\PlainText';
        return new $lang([
            'embedded' => $embedded
        ]);
    }

    /**
     * @param Language|callable|string $language
     * @param $aliases
     */
    public static function registerLanguage($language, $aliases) {
        self::$_languages = array_merge(self::$_languages, array_fill_keys($aliases, $language));
    }

    public static function setDefaultFormatter(FormatterInterface $formatter) {
        self::$_formatter = $formatter;
    }

    public static function registeredLanguages() {
        return self::$_languages;
    }

    public static function getDefaultFormatter() {
        return self::$_formatter;
    }

    public static function highlight($source, $language, FormatterInterface $formatter = null) {
        $formatter = $formatter ?: self::getDefaultFormatter();

        if(!$language instanceof Language) {
            $language = self::getLanguage($language);
        }

        return $formatter->format($language->parse($source));
    }
}

# Acts like static constructor

KeyLighter::setDefaultFormatter(
    php_sapi_name() === 'cli' ?
        new CliFormatter() :
        new HtmlFormatter()
);

KeyLighter::registerLanguage('Kadet\\Highlighter\\Language\\PhpLanguage', ['php']);
KeyLighter::registerLanguage('Kadet\\Highlighter\\Language\\XmlLanguage', ['xml', 'xaml']);
KeyLighter::registerLanguage('Kadet\\Highlighter\\Language\\HtmlLanguage', ['html', 'htm']);
KeyLighter::registerLanguage('Kadet\\Highlighter\\Language\\PowerShellLanguage', ['powershell', 'posh', 'ps1']);
KeyLighter::registerLanguage('Kadet\\Highlighter\\Language\\PlainText', ['plaintext', 'text', 'none', 'txt']);
KeyLighter::registerLanguage('Kadet\\Highlighter\\Language\\LatexLanguage', ['tex', 'latex']);
KeyLighter::registerLanguage('Kadet\\Highlighter\\Language\\IniLanguage', ['ini', 'cfg']);
KeyLighter::registerLanguage('Kadet\\Highlighter\\Language\\JavaScriptLanguage', ['js', 'jscript', 'javascript']);