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


use Kadet\Highlighter\Parser\TokenFactoryInterface;

class CommentMatcher implements MatcherInterface
{
    private $singleLine = [];
    private $multiLine  = [];

    /**
     * CommentMatcher constructor.
     *
     * @param array $singleLine
     * @param array $multiLine
     */
    public function __construct(array $singleLine, array $multiLine)
    {
        $this->singleLine = $singleLine;
        $this->multiLine = $multiLine;
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
        $all = [];

        foreach ($this->multiLine as $name => $comment) {
            $comment = array_map(function ($e) { return preg_quote($e, '/'); }, $comment);

            $all[] = [$name, "/{$comment[0]}(.*?){$comment[1]}/ms"];
        }

        foreach ($this->singleLine as $name => $comment) {
            $comment = preg_quote($comment, '/');
            $all[] = [$name, "/{$comment}(.*)/"];
        }

        foreach ($all as $i => $comment) {
            $matches = [];

            $name = $comment[0];
            $name = is_string($name) ? $name : null;
            $regex = $comment[1];

            if (preg_match_all($regex, $source, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $match) {
                    yield $factory->create(['pos' => $match[1], 'length' => strlen($match[0]), 'index' => $i, $name]);
                }
            }
        }
    }
}