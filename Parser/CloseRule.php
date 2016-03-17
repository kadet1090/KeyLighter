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


class CloseRule extends Rule
{
    /**
     * @param $source
     *
     * @return Token[]
     */
    public function match($source)
    {
        $this->factory->setType(TokenFactory::END);
        return parent::match($source);
    }
}