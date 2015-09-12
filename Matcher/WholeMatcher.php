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

namespace Kadet\Highlighter\Matcher;


use Kadet\Highlighter\Parser\Token;

class WholeMatcher implements MatcherInterface
{

    /**
     * Matches all occurrences and returns token list
     *
     * @param string $source Source to match tokens
     *
     * @param        $class
     *
     * @return array
     */
    public function match($source, $class)
    {
        $token = new $class(['pos' => 0, 'length' => strlen($source)]);

        return [$token, $token->getEnd()];
    }
}