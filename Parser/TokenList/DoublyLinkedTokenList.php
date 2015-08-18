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

class DoublyLinkedTokenList extends \SplDoublyLinkedList implements TokenListInterface, FixableTokenList
{
    use Fixable;

    private $_pos;

    private function _removeCurrent() {
        $this->next();
        $this->offsetUnset($this->_pos-1);
        $this->prev();
    }

    public function remove(AbstractToken $token)
    {
        if($token == $this->current()) {
            $this->_removeCurrent();
            return;
        }
    }

    public function save($tokens, $prefix, Rule $rule)
    {
        $this->rewind();
        foreach ($tokens as $token) {
            $token->name = $prefix . (isset($token->name) ? '.' . $token->name : '');
            $token->rule = $rule;

            $current = $token instanceof Token ? $token->split() : [$token];
            foreach($current as $t) {
                $this->insertToken($t);
            }
        }
    }

    private function insertToken(AbstractToken $token) {
        if ($this->count() == 0) {
            $this->push($token);
            return;
        }

        if(AbstractToken::compare($token, $this->top()) > 0) {
            $this->push($token);
            return;
        }

        if(AbstractToken::compare($token, $this->bottom()) < 0) {
            $this->unshift($token);
            $this->prev();
            return;
        }

        if (!$this->valid()) {
            $this->rewind();
        }

        $current = $this->current();

        $mode = AbstractToken::compare($token, $current);
        while($this->valid()) {
            if(($result = AbstractToken::compare($token, $this->current())) != $mode) {
                $this->add($this->_pos+($result > 0), $token);
                if($mode > 0) {
                    $this->prev();
                }

                return;
            }
            $mode > 0 ? $this->next() : $this->prev();
        }

        $this->push($token);
    }

    public function next() {
        parent::next();
        $this->_pos++;
        if (@!$this->current()->valid && $this->valid()) {
            $this->prev();
            $this->offsetUnset($this->_pos+1);
            $this->next();
        }
    }

    public function prev() {
        parent::prev();
        $this->_pos--;
    }

    public function rewind() {
        parent::rewind();
        $this->_pos = 0;
    }

    public function add($index, $newval)
    {
        parent::add($index, $newval);
        if($this->_pos >= $index) {
            $this->_pos++;
        }
    }

    public function unshift($value)
    {
        parent::unshift($value);
        $this->_pos++;
    }

    public function offsetUnset($index)
    {
        parent::offsetUnset($index);
        if($this->_pos >= $index) {
            $this->_pos--;
        }
    }
}