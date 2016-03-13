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

interface TokenFactoryInterface
{
    /**
     * @param $params
     *
     * @return Token
     */
    public function create($params);

    public function setRule($rule);
    public function setClass($class);
    public function setBase($base);
    public function setOffset($base);
}