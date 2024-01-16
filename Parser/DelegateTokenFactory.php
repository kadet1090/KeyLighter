<?php

declare(strict_types=1);

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

class DelegateTokenFactory implements TokenFactoryInterface
{
    private $_callable;
    private $_factory;

    /**
     * DelegateTokenFactory constructor.
     *
     * @param callable(TokenFactoryInterface, array $params): (Token|null) $function
     * @param TokenFactoryInterface|null                                   $factory
     */
    public function __construct(callable $function, TokenFactoryInterface $factory = null)
    {
        $this->_factory  = $factory ?: new TokenFactory(Token::class);
        $this->_callable = $function;
    }

    /**
     * @param       $name
     * @param array $params
     *
     * @return Token|null
     */
    public function create($name, $params = [])
    {
        $callable = $this->_callable;
        return $callable($this->_factory, $params);
    }

    public function setRule($rule)
    {
        $this->_factory->setRule($rule);
    }

    public function setClass($class)
    {
        $this->_factory->setClass($class);
    }

    public function setBase($base)
    {
        $this->_factory->setBase($base);
    }

    public function setType($type)
    {
        $this->_factory->setType($type);
    }
}
