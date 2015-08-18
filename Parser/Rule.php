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
            'priority' => 1
        ], $options);

        $this->_context  = $options['context'];
        $this->_priority = $options['priority'];
    }

    public function match($source) {
        return $this->_matcher->match($source);
    }

    public function validateContext($context) {
        if (empty($this->_context)) {
            return empty($context);
        }

        foreach ($this->_context as $rule) {
            $type = $this->_getType($rule);
            if($type !== 'in') {
                $rule = substr($rule, 1);
            }

            $matching = array_filter($context, function ($a) use ($rule) {
                return (bool)preg_match('/^'.preg_quote($rule).'(?:\\.\\w+)*$/', $a);
            });

            if($type === 'not in') {
                if(!empty($matching)) {
                    return false;
                }
            } elseif ($type === 'in') {
                if(empty($matching)) {
                    return false;
                }
            }
        }

        return true;
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
}