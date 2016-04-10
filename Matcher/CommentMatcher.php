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
    public function __construct(array $singleLine = null, array $multiLine = null)
    {
        $this->singleLine = $singleLine ?: [];
        $this->multiLine  = $multiLine  ?: [];
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

            $all[] = [$name, "/({$comment[0]}(.*?){$comment[1]})/ms"];
        }

        foreach ($this->singleLine as $name => $comment) {
            $comment = preg_quote($comment, '/');
            $all[]   = [$name, "/({$comment}.*?)$/m"];
        }

        foreach ($all as $i => $comment) {
            $matches = [];

            $name  = $comment[0];
            $name  = is_string($name) ? $name : null;
            $regex = $comment[1];

            if (preg_match_all($regex, $source, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[1] as $match) {
                    yield $factory->create(
                        $name, ['pos' => $match[1], 'length' => strlen($match[0])]
                    );
                }
            }
        }
    }
}
