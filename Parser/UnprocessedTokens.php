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

class UnprocessedTokens
{
    private $_tokens  = [];
    private $_pending = [];

    private function _push(Token $token, $offset = 0)
    {
        $token->pos += $offset;
        if (!isset($this->_tokens[$token->pos])) {
            $this->_tokens[$token->pos] = [];
        } else {
            $this->_pending[$token->pos] = true;
        }

        $this->_tokens[$token->pos][$token->id] = $token;

        return $this;
    }

    public function add(Token $token, $offset = 0)
    {
        $this->_push($token, $offset);
        if ($token->getEnd()) {
            $this->_push($token->getEnd(), $offset);
        }

        return $this;
    }

    public function batch($tokens, $offset = 0)
    {
        foreach ($tokens as $token) {
            $this->_push($token, $offset);
        }

        return $this;
    }

    public function sort()
    {
        ksort($this->_tokens);

        foreach (array_keys($this->_pending) as $position) {
            uasort(
                $this->_tokens[$position],
                Token::class . '::compare'
            );
        }
        $this->_pending = [];

        return $this;
    }

    public function toArray()
    {
        return count($this->_tokens) > 0 ? call_user_func_array('array_replace', $this->_tokens) : $this->_tokens;
    }
}
