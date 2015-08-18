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
            $rule = $token->rule;

            if ($token instanceof StartToken) {
                if ($rule->validateContext($context)) {
                    $context[$token->id] = $token->name;
                } else {
                    $this->remove($token);

                    /** @noinspection PhpUndefinedFieldInspection Bug */
                    $this->remove($token->end);
                }
            } elseif ($token instanceof EndToken) {
                unset($context[$token->id]);
            }
        }
    }
}