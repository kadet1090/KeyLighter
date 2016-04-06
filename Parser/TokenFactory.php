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
     * @param       $name
     * @param array $params
     *
     * @return false|Token|null
     */
    public function create($name, $params = [ ])
    {
        $name = $name !== null ? $this->getName($name) : $this->_base;

        if (!isset($params['rule'])) {
            $params['rule'] = $this->_rule;
        }

        if (isset($params['pos'])) {
            $params['pos'] += $this->_offset;
        }

        $class = isset($params['class']) ? $params['class'] : $this->_class;
        $end   = isset($params['end'])   ? $params['end']   : false;
        $start = isset($params['start']) ? $params['start'] : false;

        // we don't want to pass that into token
        unset($params['class'], $params['end'], $params['start']);

        if($this->_type & Token::START) {
            if(!$start) {
                $start = new $class($name, $params);
            }

            if($this->_type === Token::START) {
                $start->setEnd(false);
                return $start;
            }
        }

        if($this->_type & Token::END) {
            if (isset($params['length'])) {
                $length = $params['length'];
                unset($params['length']);

                $end = $params;
                $end['pos'] += $length;

                /** @var Token $end */
                $end = new $class($name, $end);
            } elseif(!$end) {
                $end = new $class($name, $params);
            }

            if($this->_type === Token::END) {
                $end->setStart(false);
                return $end;
            }
        }

        $start->setEnd($end);
        return $start;
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
