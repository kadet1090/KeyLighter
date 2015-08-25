<?php
/**
 * Highlighter
 *
 * Copyright (C) 2015, Some right reserved.
 * @author Kacper "Kadet" Donat <kadet1090@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/legalcode CC BY-SA
 *
 * Contact with author:
 * Xmpp: kadet@jid.pl
 * E-mail: kadet1090@gmail.com
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter\Matcher;
use Kadet\Highlighter\Parser\MarkerToken;
use Kadet\Highlighter\Utils\StringHelper;

/**
 * Class StringMatcher
 * @package Kadet\Highlighter\Matcher
 *
 * Matches all string occurrences with escaped characters.
 */
class QuoteMatcher implements MatcherInterface
{
    protected $_quotes;

    /**
     * StringMatcher constructor.
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
     * @return array
     */
    public function match($source)
    {
        $tokens = [];
        $pos = 0;

        while (($pos = StringHelper::find($source, array_values($this->_quotes), $pos, $match)) !== false) {
            $length = strlen($match);

            $token = new MarkerToken(['pos' => $pos, 'length' => $length, array_search($match, $this->_quotes, false)]);
            $tokens[] = $token;
            $tokens[] = $token->getEnd();
            $pos += $length;
        }

        return $tokens;
    }
}