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

namespace Kadet\KeyLighter\Tests\Matchers;

use Kadet\Highlighter\Matcher\CommentMatcher;
use Kadet\KeyLighter\Tests\MatcherTestCase;

require_once __DIR__.'/../MatcherTestCase.php';

class CommentMatcherTest extends MatcherTestCase
{
    public function testSingleLine() {
        $source = <<<SOURCE
test // comment
test # comment
SOURCE;
        $matcher = new CommentMatcher(['//', '#'], []);

        $this->assertTokens([
            ['start', 'pos' => 5],
            ['end', 'pos' => 16],
            ['start', 'pos' => 22],
            ['end', 'pos' => 31],
        ], $matcher->match($source, $this->getFactory()));
    }

    public function testMultiLine() {
        $source = <<<SOURCE
test /*
test
*/
SOURCE;
        $matcher = new CommentMatcher([], [['/*', '*/']]);

        $this->assertTokens([
            ['start', 'pos' => 5],
            ['end', 'pos' => 17],
        ], $matcher->match($source, $this->getFactory()));
    }

    public function testNamedSingle() {
        $source = <<<SOURCE
test /* test */ {# test2 #}
SOURCE;
        $matcher = new CommentMatcher([], ['first' => ['/*', '*/'], 'second' => ['{#', '#}']]);

        $this->assertTokens([
            ['start', 'pos' => 5, 'name' => 'first'],
            ['end', 'pos' => 15, 'name' => 'first'],
            ['start', 'pos' => 16, 'name' => 'second'],
            ['end', 'pos' => 27, 'name' => 'second'],
        ], $matcher->match($source, $this->getFactory()));
    }

    public function testNamedMulti() {
        $source = <<<SOURCE
test // comment
test # comment
SOURCE;
        $matcher = new CommentMatcher(['first' => '//', 'second' => '#'], []);

        $this->assertTokens([
            ['start', 'pos' => 5, 'name' => 'first'],
            ['end', 'pos' => 16, 'name' => 'first'],
            ['start', 'pos' => 22, 'name' => 'second'],
            ['end', 'pos' => 31, 'name' => 'second'],
        ], $matcher->match($source, $this->getFactory()));
    }
}
