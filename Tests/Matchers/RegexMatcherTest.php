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

namespace Kadet\Highlighter\Tests\Matchers;

use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Tests\MatcherTestCase;

class RegexMatcherTest extends MatcherTestCase
{
    public function testSimple()
    {
        $matcher = new RegexMatcher('/(\w+)/');
        $source  = 'word1 word2';

        $this->assertTokens([
            ['start', 'pos' => 0],
            ['end', 'pos' => 5],
            ['start', 'pos' => 6],
            ['end', 'pos' => 11],
        ], $matcher->match($source, $this->getFactory()));
    }

    public function testWithGroups()
    {
        $matcher = new RegexMatcher('/(\w+):(\w+)/', [
            1 => 'first',
            2 => 'second'
        ]);

        $source = 'x:20';
        $this->assertTokens([
            ['start', 'pos' => 0, 'name' => 'first'],
            ['end', 'pos' => 1, 'name' => 'first'],
            ['start', 'pos' => 2, 'name' => 'second'],
            ['end', 'pos' => 4, 'name' => 'second'],
        ], $matcher->match($source, $this->getFactory()));
    }

    public function testWithOptionalGroups()
    {
        $matcher = new RegexMatcher('/(\w+)(?::(\d+))?/', [
            1 => 'first',
            2 => 'second'
        ]);

        $source = 'x:20 d:[]';
        $this->assertTokens([
            ['start', 'pos' => 0, 'name' => 'first'],
            ['end', 'pos' => 1, 'name' => 'first'],
            ['start', 'pos' => 2, 'name' => 'second'],
            ['end', 'pos' => 4, 'name' => 'second'],
            ['start', 'pos' => 5, 'name' => 'first'],
            ['end', 'pos' => 6, 'name' => 'first']
        ], $matcher->match($source, $this->getFactory()));
    }
}
