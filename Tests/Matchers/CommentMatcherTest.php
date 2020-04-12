<?php

declare(strict_types=1);

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

use Kadet\Highlighter\Matcher\CommentMatcher;
use Kadet\Highlighter\Tests\MatcherTestCase;

class CommentMatcherTest extends MatcherTestCase
{
    public function testSingleLine()
    {
        $source  = "test // comment\ntest # comment";
        $matcher = new CommentMatcher(['//', '#'], []);

        $this->assertTokens([
            ['start', 'pos' => 5],
            ['end', 'pos' => 15],
            ['start', 'pos' => 21],
            ['end', 'pos' => 30],
        ], $matcher->match($source, $this->getFactory()));
    }

    public function testMultiLine()
    {
        $source  = "test /*\ntest\n*/";
        $matcher = new CommentMatcher([], [['/*', '*/']]);

        $this->assertTokens([
            ['start', 'pos' => 5],
            ['end', 'pos' => 15],
        ], $matcher->match($source, $this->getFactory()));
    }

    public function testNamedMulti()
    {
        $source  = "test /* test */ {# test2 #}";
        $matcher = new CommentMatcher([], ['first' => ['/*', '*/'], 'second' => ['{#', '#}']]);

        $this->assertTokens([
            ['start', 'pos' => 5, 'name' => 'first'],
            ['end', 'pos' => 15, 'name' => 'first'],
            ['start', 'pos' => 16, 'name' => 'second'],
            ['end', 'pos' => 27, 'name' => 'second'],
        ], $matcher->match($source, $this->getFactory()));
    }

    public function testNamedSingle()
    {
        $source  = "test // comment\ntest # comment";
        $matcher = new CommentMatcher(['first' => '//', 'second' => '#'], []);

        $this->assertTokens([
            ['start', 'pos' => 5, 'name' => 'first'],
            ['end', 'pos' => 15, 'name' => 'first'],
            ['start', 'pos' => 21, 'name' => 'second'],
            ['end', 'pos' => 30, 'name' => 'second'],
        ], $matcher->match($source, $this->getFactory()));
    }
}
