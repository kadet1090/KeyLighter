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

trait Fixable
{
    public function fix()
    {
        if (!($this instanceof TokenListInterface)) {
            return;
        }

        $context = [];
        /** @var Token $token */
        foreach($this as $token) {
            $id = $token->getId();
            if (array_key_exists($id, $context) && $token->isEnd()) {
                $copy = $context;
                unset($copy[$id]);

                if($token->getRule()->validateContext($copy)) {
                    unset($context[$id]);
                }
            } elseif ($token->isStart() && $token->getRule()->validateContext($context)) {
                $context[$id] = $token->name;
            } else {
                $token->invalidate();
                $this->remove($token);
            }
        }
    }
}