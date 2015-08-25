<?php
/**
 * Highlighter
 *
 * Copyright (C) 2015, Some right reserved.
 * @author Kacper "Kadet" Donat <kadet1090@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/legalcode CC BY-SA
 *
 * Contact with author:
 * Xmpp: kadet@jid.pl
 * E-mail: kadet1090@gmail.com
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter\Parser;


use Kadet\Highlighter\Matcher\MatcherInterface;

class Rule
{
    private $_matcher;
    private $_context = [];

    private $_priority;
    private $_language;

    /**
     * @param MatcherInterface $matcher
     * @param array $options
     */
    public function __construct(MatcherInterface $matcher, array $options = [])
    {
        $this->_matcher = $matcher;

        // Default options:
        $options = array_merge([
            'context'  => [],
            'priority' => 1,
            'language' => 'plaintext'
        ], $options);

        $this->setContext($options['context']);
        $this->_priority = $options['priority'];
        $this->_language = $options['language'];
    }

    public function match($source) {
        return $this->_matcher->match($source);
    }

    // todo: write it better
    public function validateContext($current, array $additional = []) {
        $required = array_merge($additional, $this->_context);

        list($language, $context) = $current;

        if($language !== 'language.'.$this->_language) {
            return false;
        }

        if (empty($required)) {
            return count($context) == 1;
        }

        if ($this->_language != null) {
            $required[] = 'language.'.$this->_language;
        }

        reset($required);
        while(list($rule, $type) = each($required)) {
            $matching = array_filter($context, function ($a) use ($rule) {
                return $a == $rule || fnmatch($rule.'.*', $a);
            });

            if($type === 'not in') {
                if(!empty($matching)) {
                    return false;
                }
            } elseif ($type === 'in') {
                if(empty($matching)) {
                    return false;
                }

                if(($down = strstr($rule, '.', true)) !== false) {
                    unset($required[$down]);
                }
            }
        }

        return true;
    }

    public function setContext($rules) {
        $this->_context = [];
        foreach($rules as $key => $rule) {
            if(!is_int($key)) {
                continue;
            }

            $type = $this->_getType($rule);
            if($type !== 'in') {
                $rule = substr($rule, 1);
            }

            $this->_context[$rule] = $type;
        }
    }

    private function _getType($rule) {
        // Possible more types
        switch($rule[0]) {
            case '!': return 'not in';
            case '^': return 'top';
            default:  return 'in';
        }
    }

    public function getPriority() {
        return $this->_priority;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->_language = $language;
    }
}