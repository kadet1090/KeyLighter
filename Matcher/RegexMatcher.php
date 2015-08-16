<?php
/**
 * Created by PhpStorm.
 * User: k_don
 * Date: 16.08.2015
 * Time: 16:54
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
            $result[] = new Token(['pos' => $match[1], 'length' => strlen($match[0])]);
        }

        return $result;
    }
}