<?php
/**
 * Highlighter
 *
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

use Kadet\Highlighter\Parser\MarkerToken;
use Kadet\Highlighter\Parser\TokenFactoryInterface;

/**
 * Class StringMatcher
 *
 * @package Kadet\Highlighter\Matcher
 *
 * Matches all string occurrences with escaped characters.
 */
class QuoteMatcher implements MatcherInterface
{
    protected $_quotes;

    /**
     * StringMatcher constructor.
     *
     * @param string[] $quotes possible quotes for string
     */
    public function __construct(array $quotes = ['\'', '"'])
    {
        $this->_quotes = $quotes;
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
        $tokens = [];
        foreach ($this->_quotes as $name => $quote) {
            $pos = 0;
            $length = strlen($quote);

            while (($pos = strpos($source, $quote, $pos)) !== false) {
                $token = $factory->create(['pos' => $pos, 'length' => $length, $name]);
                $end = $token->getEnd();

                $tokens[spl_object_hash($token)] = $token;
                $tokens[spl_object_hash($end)]   = $end;
                $pos += $length;
            }
        }

        return $tokens;
    }
}