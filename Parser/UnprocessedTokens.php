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

class UnprocessedTokens
{
    private $_tokens  = [];
    private $_pending = [];

    private function _push(Token $token)
    {
        if (!isset($this->_tokens[$token->pos])) {
            $this->_tokens[$token->pos] = [];
        } else {
            $this->_pending[$token->pos] = true;
        }

        $this->_tokens[$token->pos][spl_object_hash($token)] = $token;

        return $this;
    }

    public function add(Token $token)
    {
        $this->_push($token);
        if ($token->getEnd()) {
            $this->_push($token->getEnd());
        }

        return $this;
    }

    public function batch($tokens)
    {
        foreach ($tokens as $token) {
            $this->_push($token);
        }

        return $this;
    }

    public function sort()
    {
        ksort($this->_tokens);

        foreach (array_keys($this->_pending) as $position) {
            uasort(
                $this->_tokens[$position],
                Token::class.'::compare'
            );
        }
        $this->_pending = [];

        return $this;
    }

    public function toArray()
    {
        return call_user_func_array('array_merge', $this->_tokens);
    }
}
