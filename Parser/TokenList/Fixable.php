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
use Kadet\Highlighter\Parser\EndToken;
use Kadet\Highlighter\Parser\StartToken;

trait Fixable
{
    public function fix()
    {
        if (!($this instanceof TokenListInterface)) {
            return;
        }

        $context = [];
        /** @var AbstractToken $token */
        foreach($this as $token) {
            if ($token instanceof EndToken && array_key_exists($token->id, $context)) {
                $copy = $context;
                unset($copy[$token->id]);

                if($token->rule->validateContext($copy)) {
                    unset($context[$token->id]);
                }
            } elseif ($token instanceof StartToken && $token->rule->validateContext($context)) {
                $context[$token->id] = $token->name;
            } else {
                $token->invalidate();
                $this->remove($token);
            }
        }
    }
}