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

namespace Kadet\Highlighter\Parser;

use Kadet\Highlighter\Parser\Token\Token;

/**
 * Factory used to handle various token creation.
 *
 * @package Kadet\Highlighter\Parser
 */
class TokenFactory implements TokenFactoryInterface
{
    private $_class;
    private $_base;
    private $_rule;
    private $_type = 0x3;

    private $_cache = [];

    /**
     * Constructor
     *
     * @param string $class {@see }
     */
    public function __construct($class)
    {
        $this->setClass($class);
    }

    /**
     * @param       $name
     * @param array $params
     *
     * @return false|Token|null
     */
    public function create($name, $params = [ ])
    {
        if ($name !== null) {
            $name = $this->getName($name);
        } else {
            $name = $this->_base;
        }

        if (!isset($params['rule'])) {
            $params['rule'] = $this->_rule;
        }

        if (!isset($params['class'])) {
            $params['class'] = $this->_class;
        }

        return $this->link($name, $params);
    }

    private function link($name, $params)
    {
        if ($this->_type & Token::START) {
            if (!isset($params['start'])) {
                $params['start'] = new $params['class']($name, $params);
            }

            if ($this->_type === Token::START) {
                $params['start']->setEnd(false);
                return $params['start'];
            }
        }

        if ($this->_type & Token::END) {
            if (isset($params['length'])) {
                $end = $params;
                $end['pos'] += $params['length'];

                /** @var Token $end */
                $params['end'] = new $params['class']($name, $end);
            } elseif (!isset($params['end'])) {
                $params['end'] = new $params['class']($name, $params);
            }

            if ($this->_type === Token::END) {
                $params['end']->setStart(false);
                return $params['end'];
            }
        }

        $params['start']->setEnd($params['end']);
        return $params['start'];
    }

    private function getName($name)
    {
        if (!isset($this->_cache[$name])) {
            $this->_cache[$name] = str_replace('$', $this->_base, $name);
        }

        return $this->_cache[$name];
    }

    /**
     * @param string $base
     */
    public function setBase($base)
    {
        $this->_cache = []; // invalidate cache
        $this->_base  = $base;
    }

    /**
     * @param mixed $rule
     */
    public function setRule($rule)
    {
        $this->_rule = $rule;
    }

    /**
     * @param mixed $class
     *
     * @throws \InvalidArgumentException
     */
    public function setClass($class)
    {
        if (!is_a($class, Token::class, true)) {
            throw new \InvalidArgumentException('$class must extend Kadet\Highlighter\Parser\Token\Token');
        }

        $this->_class = $class;
    }

    public function setType($type)
    {
        $this->_type = $type;
    }
}
