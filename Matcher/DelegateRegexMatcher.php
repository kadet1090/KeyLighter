<?php

/**
 * Highlighter
 *
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

namespace Kadet\Highlighter\Matcher;

use Kadet\Highlighter\Parser\TokenFactoryInterface;

class DelegateRegexMatcher implements MatcherInterface
{
    private $regex;
    private $callable;

    /**
     * RegexMatcher constructor.
     *
     * @param          $regex
     * @param callable $callable
     */
    public function __construct($regex, callable $callable)
    {
        $this->regex    = $regex;
        $this->callable = $callable;
    }

    /**
     * Matches all occurrences and returns token list
     *
     * @param string                $source Source to match tokens
     *
     * @param TokenFactoryInterface $factory
     *
     * @return \Generator
     */
    public function match($source, TokenFactoryInterface $factory)
    {
        preg_match_all($this->regex, $source, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        $callable = $this->callable;
        foreach ($matches as $match) {
            foreach ($callable($match, $factory) as $token) {
                yield $token;
            }
        }
    }
}
