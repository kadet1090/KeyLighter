<?php

/**
 * Highlighter
 *1
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
