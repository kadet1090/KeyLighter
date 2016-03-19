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

namespace Kadet\Highlighter\Parser;


use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Matcher\MatcherInterface;

/**
 * Class Rule
 *
 * @package Kadet\Highlighter\Parser
 *
 * @property Language              $language
 * @property Language              $inject
 * @property integer               $priority
 * @property string                $type
 * @property TokenFactoryInterface $factory
 */
class Rule
{
    const CONTEXT_IN        = 1;
    const CONTEXT_NOT_IN    = 2;
    const CONTEXT_IN_ONE_OF = 4;
    const CONTEXT_EXACTLY   = 8;

    private $_matcher;
    private $_context = [];

    private $_default = true;

    private $_options;

    /**
     * @param MatcherInterface|null $matcher
     * @param array                 $options
     */
    public function __construct(MatcherInterface $matcher = null, array $options = [])
    {
        $this->_matcher = $matcher;

        // Default options:
        $options = array_merge([
            'context'  => [],
            'priority' => 1,
            'language' => false,
            'factory'  => new TokenFactory('\Kadet\Highlighter\Parser\Token'),
        ], $options);

        $this->setContext($options['context']);
        $this->_options = $options;

        $this->factory->setRule($this);
    }

    public function setContext($rules)
    {
        $this->_context = [];
        foreach ($rules as $key => $rule) {
            list($plain, $type) = $this->_getContextRule($rule);
            $this->_context[$plain] = $type;
        }
    }

    private function _getContextRule($rule)
    {
        $types = [
            '!' => self::CONTEXT_NOT_IN,
            '+' => self::CONTEXT_IN,
            '*' => self::CONTEXT_IN_ONE_OF,
            '@' => self::CONTEXT_EXACTLY,
        ];

        if (!isset($types[$rule[0]])) {
            return [$rule, self::CONTEXT_IN];
        }

        $type = 0;
        $pos = 0;
        foreach (str_split($rule) as $pos => $char) {
            if (!isset($types[$char])) {
                break;
            }

            if ($types[$char] == self::CONTEXT_IN_ONE_OF) {
                $this->_default = false;
            }

            $type |= $types[$char];
        }

        return [substr($rule, $pos), $type];
    }

    /**
     * @param $source
     *
     * @return Token[]
     */
    public function match($source)
    {
        return $this->_matcher !== null ? $this->_matcher->match($source, $this->factory) : [];
    }

    public function validateContext($context, array $additional = [])
    {
        $required = array_merge($additional, $this->_context);

        if (empty($required)) {
            return count($context) === 0;
        }

        $result = $this->_default;

        reset($required);
        while (list($rule, $type) = each($required)) {
            $matched = !($type & self::CONTEXT_EXACTLY) ?
                count(array_filter($context, function ($a) use ($rule) {
                    return $a === $rule || fnmatch($rule . '.*', $a);
                })) > 0 :
                in_array($rule, $context, true);

            if ($type & self::CONTEXT_NOT_IN) {
                if ($matched) {
                    return false;
                }
                $result = true;
            } elseif ($type & self::CONTEXT_IN) {
                if (!$matched) {
                    return false;
                }
                $result = true;

                $this->_unsetUnnecessaryRules($rule, $required);
            } elseif ($type & self::CONTEXT_IN_ONE_OF) {
                if ($matched) {
                    $result = true;
                    $this->_unsetUnnecessaryRules($rule, $required);
                }
            }
        }

        return $result;
    }

    private function _unsetUnnecessaryRules($rule, &$required)
    {
        if (strpos($rule, '.') !== false) {
            foreach (array_filter(array_keys($this->_context), function ($key) use ($rule) {
                return fnmatch($key . '.*', $rule);
            }) as $remove) {
                unset($required[$remove]);
            }
        }
    }

    public function __get($option)
    {
        return isset($this->_options[$option]) ? $this->_options[$option] : null;
    }

    public function __set($option, $value)
    {
        return $this->_options[$option] = $value;
    }
}