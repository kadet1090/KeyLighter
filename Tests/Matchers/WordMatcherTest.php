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

use Kadet\Highlighter\Matcher\WordMatcher;
use Kadet\Highlighter\Tests\MatcherTestCase;

class WordMatcherTest extends MatcherTestCase
{
    public function testSimple()
    {
        $source  = 'first second seconder';
        $matcher = new WordMatcher(['first', 'second']);

        $this->assertTokens([
            ['start', 'pos' => 0],
            ['end', 'pos' => 5],
            ['start', 'pos' => 6],
            ['end', 'pos' => 12],
        ], $matcher->match($source, $this->getFactory()));
    }

    public function testAtomic()
    {
        $source  = 'second seconder';
        $matcher = new WordMatcher(['second', 'seconder'], [
            'atomic'    => true,
            'separated' => false
        ]);

        $this->assertTokens([
            ['start', 'pos' => 0],
            ['end', 'pos' => 6],
            ['start', 'pos' => 7],
            ['end', 'pos' => 13],
        ], $matcher->match($source, $this->getFactory()));
    }

    public function testNonSeparated()
    {
        $source  = 'first firster';
        $matcher = new WordMatcher(['first'], [
            'separated' => false
        ]);

        $this->assertTokens([
            ['start', 'pos' => 0],
            ['end', 'pos' => 5],
            ['start', 'pos' => 6],
            ['end', 'pos' => 11],
        ], $matcher->match($source, $this->getFactory()));
    }

    public function testCaseInsensitive()
    {
        $source  = 'first FIRST';
        $matcher = new WordMatcher(['first']);

        $this->assertTokens([
            ['start', 'pos' => 0],
            ['end', 'pos' => 5],
            ['start', 'pos' => 6],
            ['end', 'pos' => 11],
        ], $matcher->match($source, $this->getFactory()));
    }

    public function testCaseSensitive()
    {
        $source  = 'first FIRST';
        $matcher = new WordMatcher(['first'], [
            'case-sensitivity' => true
        ]);

        $this->assertTokens([
            ['start', 'pos' => 0],
            ['end', 'pos' => 5],
        ], $matcher->match($source, $this->getFactory()));
    }

    public function testNonEscaped()
    {
        $source  = 'first abcdef';
        $matcher = new WordMatcher(['\w+'], [
            'escape' => false
        ]);

        $this->assertTokens([
            ['start', 'pos' => 0],
            ['end', 'pos' => 5],
            ['start', 'pos' => 6],
            ['end', 'pos' => 12],
        ], $matcher->match($source, $this->getFactory()));
    }

    public function testGetWords()
    {
        $matcher = new WordMatcher($words = ['a', 'bc', 'def']);

        $this->assertSame($words, $matcher->getWords());
    }

    public function testGetOptions()
    {
        $matcher = new WordMatcher(['not-important'], $options = [
            'atomic'    => true,
            'separated' => false
        ]);

        $this->assertSame($options, $matcher->getOptions());
    }

    public function testMerge()
    {
        $matcher = new WordMatcher($words = ['a', 'b', 'c'], $options = [
            'atomic'    => true,
            'separated' => false
        ]);

        $merged = $matcher->merge(['d', 'e']);

        $this->assertSame(['a', 'b', 'c', 'd', 'e'], $merged->getWords());
        $this->assertSame($options, $merged->getOptions());
    }

    public function testSubtract()
    {
        $matcher = new WordMatcher($words = ['a', 'b', 'c', 'd', 'e'], $options = [
            'atomic'    => true,
            'separated' => false
        ]);

        $merged = $matcher->subtract(['d', 'e']);

        $this->assertSame(['a', 'b', 'c'], $merged->getWords());
        $this->assertSame($options, $merged->getOptions());
    }
}
