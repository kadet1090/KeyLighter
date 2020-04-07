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

declare(strict_types=1);

namespace Kadet\Highlighter;

use Kadet\Highlighter\Formatter\CliFormatter;
use Kadet\Highlighter\Formatter\FormatterInterface;
use Kadet\Highlighter\Formatter\HtmlFormatter;
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

    public const VERSION = '0.9-dev';

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

    private $_formatters = [];

    /** @var FormatterInterface */
    private $_formatter = null;

    public function getLanguage(string $name): Language
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

    public function languageByName(string $name, array $params = []): Language
    {
        return isset($this->_languages['name'][$name]) ?
            $this->_languages['name'][$name]($params) :
            new PlainText($params);
    }

    public function languageByMime(string $mime, array $params = []): Language
    {
        return isset($this->_languages['mime'][$mime]) ?
            $this->_languages['mime'][$mime]($params) :
            new PlainText($params);
    }

    public function languageByExt(string $filename, array $params = []): Language
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
    public function registerLanguage($language, $aliases): void
    {
        $this->register($language, ['name' => $aliases]);
    }

    public function setDefaultFormatter(FormatterInterface $formatter): void
    {
        $this->_formatter = $formatter;
    }

    public function registeredLanguages(string $by = 'name', bool $class = false): array
    {
        return array_map(function ($e) use($class) {
            return $e([])->getFQN($class);
        }, $this->_languages[$by]);
    }

    public function getDefaultFormatter(): FormatterInterface
    {
        return $this->_formatter;
    }

    public function addFormatter(string $name, FormatterInterface $formatter): void
    {
        $this->_formatters[$name] = $formatter;
    }

    public function getFormatter($name)
    {
        return isset($this->_formatters[$name]) ? $this->_formatters[$name] : false;
    }

    public function registeredFormatters(): array
    {
        return $this->_formatters;
    }

    public function highlight(string $source, Language $language, FormatterInterface $formatter = null): string
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

    public function init(): void
    {
        foreach(include __DIR__.'/Config/metadata.php' as $alias) {
            $class = $alias[0];
            unset($alias[0]);

            $this->register($class, $alias);
        }

        $this->_formatters = include __DIR__.'/Config/formatters.php';
    }

    /**
     * @param string|callable $class
     * @param array           $options
     */
    public function register($class, array $options): void
    {
        if(!is_callable($class) && is_subclass_of($class, Language::class)) {
            $class = function($arguments = []) use ($class) {
                return new $class($arguments);
            };
        }

        foreach(array_intersect_key($options, array_flip(['name', 'mime', 'extension'])) as $name => $values) {
            $this->_languages[$name] = array_merge($this->_languages[$name], array_fill_keys($values, $class));
        }
    }
}
