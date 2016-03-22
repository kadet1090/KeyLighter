<?php
/**
 * Highlighter
 *1
 * Copyright (C) 2015, Some right reserved.
 *
 * @author Kacper "Kadet" Donat <kadet1090@gmail.com>
 *
 * Contact with author:
 * Xmpp: kadet@jid.pl
 * E-mail: kadet1090@gmail.com
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter\Parser;

use Kadet\Highlighter\Parser\Token\Token;

class CloseRule extends Rule
{
    /**
     * @param $source
     *
     * @return Token[]
     */
    public function match($source)
    {
        $this->factory->setType(Token::END);

        return parent::match($source);
    }
}
