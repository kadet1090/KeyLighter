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

interface TokenFactoryInterface
{
    /**
     * @param $params
     *
     * @return Token|null
     */
    public function create($params);

    public function setRule($rule);
    public function setClass($class);
    public function setBase($base);
    public function setOffset($offset);
    public function setType($type);
}
