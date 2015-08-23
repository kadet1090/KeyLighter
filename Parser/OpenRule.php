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

namespace Kadet\Highlighter\Parser;


class OpenRule extends Rule
{
    public function match($source)
    {
        return array_filter(parent::match($source), function (Token $token) {
            if($token->isStart()) {
                $token->setEnd(null);
                return true;
            }

            return false;
        });
    }
}