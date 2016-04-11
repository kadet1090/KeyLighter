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
    public function getLanguage($name)
    {
        $embedded = [];
        if (($pos = strpos($name, '>')) !== false) {
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
    public function registerLanguage($language, $aliases)
    {
        $this->_languages = array_merge($this->_languages, array_fill_keys($aliases, $language));
    }

    public function setDefaultFormatter(FormatterInterface $formatter)
    {
        $this->_formatter = $formatter;
    }

    public function registeredLanguages()
    {
        return $this->_languages;
    }

    public function getDefaultFormatter()
    {
        return $this->_formatter;
    }

    public function highlight($source, $language, FormatterInterface $formatter = null)
    {
        $formatter = $formatter ?: $this->getDefaultFormatter();

        if (!$language instanceof GreedyLanguage) {
            $language = $this->getLanguage($language);
        }

        return $formatter->format($language->parse($source));
    }

    public function __construct()
    {
        $this->setDefaultFormatter(
            php_sapi_name() === 'cli' ? new CliFormatter() : new HtmlFormatter()
        );

        $this->registerLanguage('Kadet\\Highlighter\\Language\\Php', ['php']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\Xml', ['xml', 'xaml']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\Html', ['html', 'htm']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\PowerShell', ['powershell', 'posh', 'ps1']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\PlainText', ['plaintext', 'text', 'none', 'txt']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\Latex', ['tex', 'latex']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\Ini', ['ini', 'cfg']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\JavaScript', ['js', 'jscript', 'javascript']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\Css', ['css']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\Css\\Scss', ['scss']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\Css\\Sass', ['sass']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\Css\\Less', ['less']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\Sql', ['sql']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\Sql\\MySql', ['mysql']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\Perl', ['perl']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\C', ['c']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\Cpp', ['cpp', 'c++']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\CSharp', ['cs', 'csharp', 'c#']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\Java', ['java']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\Python', ['python', 'py']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\Python\\Django', ['django']);
        $this->registerLanguage('Kadet\\Highlighter\\Language\\Markdown', ['markdown', 'md']);
    }
}
