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

class SubStringMatcher implements MatcherInterface
{
    private $_substr;

    /**
     * SubstrMatcher constructor.
     *
     * @param $substr
     */
    public function __construct($substr)
    {
        $this->_substr = $substr;
    }


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
        $results = [];
        $pos = 0;

        $len = strlen($this->_substr);

        while (($pos = strpos($source, $this->_substr, $pos)) !== false) {
            $token = $factory->create(['pos' => $pos, 'length' => $len]);
            $end = $token->getEnd();

            $results[spl_object_hash($token)] = $token;
            $results[spl_object_hash($end)]   = $end;
            $pos += $len;
        }

        return $results;
    }
}