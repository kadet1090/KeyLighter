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


use Kadet\Highlighter\Parser\AbstractToken;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token;

class SimpleTokenList extends \ArrayObject implements TokenListInterface, FixableTokenList
{
    use Fixable {
        Fixable::fix as _fix;
    }

    private $_remove = [];

    public function remove(AbstractToken $token)
    {
        $this->_remove[] = spl_object_hash($token);
    }

    public function save($tokens, $prefix, Rule $rule)
    {
        foreach($tokens as $token) {
            $token->name = $prefix . (isset($token->name) ? '.' . $token->name : '');
            $token->rule = $rule;

            $current = $token instanceof Token ? $token->split() : [$token];
            foreach($current as $t) {
                $this[spl_object_hash($t)] = $t;
            }
        }
    }

    public function fix()
    {
        $this->uasort('\Kadet\Highlighter\Parser\AbstractToken::compare');
        $this->_fix();
        foreach($this->_remove as $hash) {
            $this->offsetUnset($hash);
        }
    }

    public function appendArray(array $array) {
        foreach($array as $value) {
            $this[] = $value;
        }
    }
}