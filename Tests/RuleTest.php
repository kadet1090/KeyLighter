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

namespace Kadet\Highlighter\Tests;

use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\Token;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    public function testMatching()
    {
        $matcher = $this->getMock('Kadet\Highlighter\Matcher\MatcherInterface');
        $tokens  = [
            new Token(['token.name', 'pos' => 15])
        ];

        $matcher->method('match')->willReturn($tokens);
        $rule = new Rule($matcher);

        $matcher->expects($this->once())->method('match')->with('source', $rule->factory);
        $this->assertEquals($rule->match("source"), $tokens);
    }

    public function testCreation()
    {
        $language = $this->getMock('Kadet\Highlighter\Language\Language');
        $factory  = $this
            ->getMockBuilder('Kadet\Highlighter\Parser\TokenFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $rule = new Rule(null, [
            'factory'  => $factory,
            'priority' => 10,
            'language' => $language
        ]);

        $this->assertSame($rule->language, $language);
        $this->assertSame($rule->priority, 10);
        $this->assertSame($rule->factory, $factory);
    }

    public function testInContextValidation()
    {
        $rule = new Rule(null, [
            'context' => ['valid']
        ]);

        $this->assertTrue($rule->validate(['valid']));
        $this->assertTrue($rule->validate(['other', 'valid']));
        $this->assertTrue($rule->validate(['other', 'valid', 'some']));
        $this->assertFalse($rule->validate(['other', 'some']));
    }

    public function testNotInContextValidation()
    {
        $rule = new Rule(null, [
            'context' => ['!invalid']
        ]);

        $this->assertTrue($rule->validate([]));
        $this->assertTrue($rule->validate(['some']));
        $this->assertFalse($rule->validate(['invalid']));
    }

    public function testInOneOfContextValidation()
    {
        $rule = new Rule(null, [
            'context' => ['*first', '*second']
        ]);

        $this->assertTrue($rule->validate(['first']));
        $this->assertTrue($rule->validate(['second']));
        $this->assertFalse($rule->validate([]));
        $this->assertFalse($rule->validate(['other']));
    }

    public function testSubTokens()
    {
        $rule = new Rule(null, [
            'context' => ['token.test', '!token']
        ]);

        $this->assertTrue($rule->validate(['token.test']));
        $this->assertFalse($rule->validate(['token']));
    }

    public function testInAll()
    {
        $rule = new Rule(null, [
            'context' => Rule::everywhere()
        ]);

        $this->assertTrue($rule->validate(['first']));
        $this->assertTrue($rule->validate(['second']));
        $this->assertTrue($rule->validate([]));
        $this->assertTrue($rule->validate(['other']));
    }

    public function testInNone()
    {
        $rule = new Rule(null);

        $this->assertFalse($rule->validate(['first']));
        $this->assertFalse($rule->validate(['second']));
        $this->assertTrue($rule->validate([]));
        $this->assertFalse($rule->validate(['other']));
    }

    public function testCallableValidator()
    {
        $rule = new Rule(null, [
            'context' => function ($context) {
                return in_array('bar', $context) && !in_array('foo', $context);
            }
        ]);

        $this->assertFalse($rule->validate(['test']));
        $this->assertTrue($rule->validate(['bar']));
        $this->assertTrue($rule->validate(['bar', 'smth']));
        $this->assertFalse($rule->validate(['bar', 'foo']));
    }

    /*public function testOpenRule() {
        $matcher = $this->getMock('Kadet\Highlighter\Matcher\MatcherInterface');

        $rule = new OpenRule($matcher);
        $token = new Token(['token.name', 'pos' => 15, 'end' => new Token(['test', 'pos' => 25]), 'rule' => $rule]);

        $tokens = [
            $token,
            $token->getEnd(),
        ];

        $matcher->method('match')->willReturn($tokens);

        $rule = new OpenRule($matcher);
        foreach ($rule->match('source') as $item) {
            $this->assertTrue($item->isStart());
            $this->assertFalse($item->isEnd());
        }
    }

    public function testCloseRule() {
        $matcher = $this->getMock('Kadet\Highlighter\Matcher\MatcherInterface');

        $rule = new CloseRule($matcher);

        $token = new Token(['token.name', 'pos' => 15, 'start' => new Token(['test', 'pos' => 25]), 'rule' => $rule]);

        $tokens = [
            $token,
            $token->getStart(),
        ];

        $matcher->method('match')->willReturn($tokens);
        foreach ($rule->match('source') as $item) {
            $this->assertTrue($item->isEnd());
            $this->assertFalse($item->isStart());
        }
    }*/
}
