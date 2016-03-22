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
    private $_offset;
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
     * @param $params
     *
     * @return Token|null
     */
    public function create($params)
    {
        $params[0] = !empty($params[0]) ? $this->getName($params[0]) : $this->_base;
        if (empty($params['rule'])) {
            $params['rule'] = $this->_rule;
        }

        if (isset($params['pos'])) {
            $params['pos'] += $this->_offset;
        }

        $end = null;

        if (isset($params['length']) && ($this->_type & Token::END)) {
            $end = $params;
            $end['pos'] += $params['length'];

            $params['end'] = new $this->_class($end);
        }

        /** @var Token $token */
        $token = new $this->_class($params);

        if ($this->_type == 0x3) {
            return $token;
        }

        if ($this->_type === Token::START) {
            $token->setEnd(false);

            return $token;
        } else {
            $token->getEnd()->setStart(false);

            return $token->getEnd();
        }
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
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->_offset = $offset;
    }

    /**
     * @param mixed $class
     *
     * @throws \InvalidArgumentException
     */
    public function setClass($class)
    {
        if (!is_a($class, 'Kadet\Highlighter\Parser\Token\Token', true)) {
            throw new \InvalidArgumentException('$class must extend Kadet\Highlighter\Parser\Token\Token');
        }

        $this->_class = $class;
    }

    public function setType($type)
    {
        $this->_type = $type;
    }
}
