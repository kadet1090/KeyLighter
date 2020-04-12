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

use Kadet\Highlighter\Matcher\DelegateRegexMatcher;
use Kadet\Highlighter\Tests\MatcherTestCase;

class DelegateRegexMatcherTest extends MatcherTestCase
{
    public function testCallableInvoking()
    {
        $mock = $this->createPartialMock('stdClass', ['test']);

        $factory = $this->getFactory();

        $mock->method('test')->willReturn([]);
        $mock->expects($this->once())->method('test')->with([
            0 => ['<foo:bar>', 0],
            1 => ['foo', 1],
            2 => ['bar', 5],
        ], $factory);

        $matcher = new DelegateRegexMatcher('/<(\w+):(\w+)>/i', [$mock, 'test']);

        iterator_to_array($matcher->match('<foo:bar>', $factory));
    }

    public function testTokenReturning()
    {
        $mock = $this->createPartialMock('stdClass', ['test']);

        $factory = $this->getFactory();

        $mock->method('test')->willReturn([$factory->create('token.name', ['pos' => 4, 'length' => 4])]);

        $matcher = new DelegateRegexMatcher('/<(\w+):(\w+)>/i', [$mock, 'test']);
        $this->assertTokens([
            ['start', 'pos' => 4, 'name' => 'token.name'],
            ['end', 'pos' => 8, 'name' => 'token.name'],
        ], $matcher->match('<foo:bar>', $factory));
    }
}
