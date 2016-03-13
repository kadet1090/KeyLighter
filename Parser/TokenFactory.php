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


class TokenFactory implements TokenFactoryInterface
{
    const START = 0x1;
    const END   = 0x2;

    private $_class;
    private $_base;
    private $_rule;
    private $_offset;

    private $_cache = [];

    /**
     * TokenFactory constructor.
     *
     * @param $class
     */
    public function __construct($class) {
        $this->setClass($class);
    }

    public function create($params) {
        $params[0] = !empty($params[0]) ? $this->getName($params[0]) : $this->_base;
        if(empty($params['rule'])) {
            $params['rule'] = $this->_rule;
        }

        if(isset($params['pos'])) {
            $params['pos'] += $this->getOffset();
        }

        return new $this->_class($params);
    }

    private function getName($name) {
        if (!isset($this->_cache[$name])) {
            $this->_cache[$name] = str_replace('$', $this->_base, $name);
        }

        return $this->_cache[$name];
    }

    /**
     * @return string
     */
    public function getBase()
    {
        return $this->_base;
    }

    /**
     * @param string $base
     */
    public function setBase($base)
    {
        $this->_cache = []; // invalidate cache
        $this->_base = $base;
    }

    /**
     * @return mixed
     */
    public function getRule()
    {
        return $this->_rule;
    }

    /**
     * @param mixed $rule
     */
    public function setRule($rule)
    {
        $this->_rule = $rule;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->_class;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->_offset;
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
        if(!is_a($class, 'Kadet\Highlighter\Parser\Token', true)) {
            throw new \InvalidArgumentException('$class must extend Kadet\Highlighter\Parser\Token');
        }

        $this->_class = $class;
    }
}