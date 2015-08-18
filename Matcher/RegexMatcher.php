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


use Kadet\Highlighter\Parser\Token;

class RegexMatcher implements MatcherInterface
{
    private $regex;

    /**
     * RegexMatcher constructor.
     * @param $regex
     */
    public function __construct($regex)
    {
        $this->regex = $regex;
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
        preg_match_all($this->regex, $source, $matches, PREG_OFFSET_CAPTURE);
        $result = [];
        foreach($matches[1] as $match) {
            $token = new Token(['pos' => $match[1], 'length' => strlen($match[0])]);

            $result[] = $token;
            $result[] = $token->getEnd();
        }

        return $result;
    }
}