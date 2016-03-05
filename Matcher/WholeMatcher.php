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
use Kadet\Highlighter\Parser\TokenFactoryInterface;

class WholeMatcher implements MatcherInterface
{

    /**
     * Matches all occurrences and returns token list
     *
     * @param string                $source Source to match tokens
     *
     * @param TokenFactoryInterface $factory
     *
     * @return array
     */
    public function match($source, TokenFactoryInterface $factory)
    {
        $token = $factory->create(['pos' => 0, 'length' => strlen($source)]);
        $end = $token->getEnd();

        return [spl_object_hash($token) => $token, spl_object_hash($end) => $end];
    }
}