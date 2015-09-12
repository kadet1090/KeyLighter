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


use Kadet\Highlighter\Parser\Token;

class RegexMatcher implements MatcherInterface
{
    private $regex;

    /**
     * RegexMatcher constructor.
     *
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
     * @param        $class
     *
     * @return array
     */
    public function match($source, $class)
    {
        preg_match_all($this->regex, $source, $matches, PREG_OFFSET_CAPTURE);
        unset($matches[0]);

        $result = [];

        while (list($name, $group) = each($matches)) {
            if(is_string($name)) {
                next($matches);
                $group = current($matches);
            } else {
                $name = null;
            }

            foreach ($group as $match) {
                $token = new $class([$name, 'pos' => $match[1], 'length' => strlen($match[0])]);

                $result[] = $token;
                $result[] = $token->getEnd();
            }
        }

        return $result;
    }
}