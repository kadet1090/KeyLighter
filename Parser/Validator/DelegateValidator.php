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

namespace Kadet\Highlighter\Parser\Validator;


class DelegateValidator extends Validator
{
    /**
     * @var callable
     */
    private $_callable;

    /**
     * DelegateValidator constructor.
     *
     * @param callable $callable
     */
    public function __construct(callable $callable)
    {
        $this->_callable = $callable;
    }

    public function validate(array $context, $additional = [])
    {
        $callable = $this->_callable;
        return $this->_validate($context, $additional, $callable($context));
    }
}