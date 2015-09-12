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
     * @param string $source Source to match tokens
     *
     * @param        $class
     *
     * @return array
     */
    public function match($source, $class)
    {
        $tokens = [];
        foreach ($this->_quotes as $name => $quote) {
            $pos = 0;
            $length = strlen($quote);

            while (($pos = strpos($source, $quote, $pos)) !== false) {
                $token = new MarkerToken(['pos' => $pos, 'length' => $length, $name]);
                $tokens[] = $token;
                $tokens[] = $token->getEnd();
                $pos += $length;
            }
        }

        return $tokens;
    }
}