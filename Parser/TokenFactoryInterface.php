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

interface TokenFactoryInterface
{
    /**
     * @param $params
     *
     * @return Token|null|false
     */
    public function create($params);

    public function setRule($rule);
    public function setClass($class);
    public function setBase($base);
    public function setOffset($offset);
    public function setType($type);
}
