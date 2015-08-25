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

class SimpleTokenList extends \ArrayObject implements TokenListInterface, FixableTokenList
{
    private $_remove = [];

    public function remove(Token $token)
    {
        $this->_remove[] = spl_object_hash($token);
    }

    public function save($tokens, Rule $rule, $prefix = null)
    {
        /** @var Token $token */
        foreach($tokens as $token) {
            $token->name = $prefix . (isset($token->name) ? '.' . $token->name : '');
            $token->setRule($rule);

            $this[spl_object_hash($token)] = $token;
        }
    }

    public function beforeParse()
    {
        $this->uasort('\Kadet\Highlighter\Parser\Token::compare');
    }

    public function appendArray(array $array) {
        foreach($array as $value) {
            $this[] = $value;
        }
    }

    public function afterParse()
    {
        foreach($this->_remove as $hash) {
            $this->offsetUnset($hash);
        }
    }

    public function get($hash)
    {
        return $this->offsetGet($hash);
    }
}