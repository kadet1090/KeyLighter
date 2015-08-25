<?php
/**
 * Highlighter
 *1
 * Copyright (C) 2015, Some right reserved.
 * @author Kacper "Kadet" Donat <kadet1090@gmail.com>
 *
 * Contact with author:
 * Xmpp: kadet@jid.pl
 * E-mail: kadet1090@gmail.com
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter\Parser\TokenList;


use Kadet\Highlighter\Parser\Token;
use Kadet\Highlighter\Parser\Rule;

interface TokenListInterface extends \Traversable
{
    public function remove(Token $token);
    public function save($tokens, Rule $rule, $prefix = null);
    public function get($hash);
}