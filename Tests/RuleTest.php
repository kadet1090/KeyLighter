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
use Kadet\Highlighter\Parser\Token;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    public function testMatching() {
        $matcher = $this->getMock('Kadet\Highlighter\Matcher\MatcherInterface');
        $tokens = [
            new Token(['token.name', 'pos' => 15])
        ];

        $matcher->method('match')->willReturn($tokens);
        $rule = new Rule($matcher);

        $matcher->expects($this->once())->method('match')->with('source', $rule->factory);
        $this->assertEquals($rule->match("source"), $tokens);
    }

    public function testCreation() {
        $language = $this->getMock('Kadet\Highlighter\Language\Language');
        $factory  = $this
            ->getMockBuilder('Kadet\Highlighter\Parser\TokenFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $rule = new Rule(null, [
            'factory' => $factory,
            'priority' => 10,
            'language' => $language
        ]);

        $this->assertSame($rule->language, $language);
        $this->assertSame($rule->priority, 10);
        $this->assertSame($rule->factory, $factory);
    }

    public function testInContextValidation() {
        $rule = new Rule(null, [
            'context' => ['valid']
        ]);

        $this->assertTrue($rule->validateContext(['valid']));
        $this->assertTrue($rule->validateContext(['other', 'valid']));
        $this->assertTrue($rule->validateContext(['other', 'valid', 'some']));
        $this->assertFalse($rule->validateContext(['other', 'some']));
    }

    public function testNotInContextValidation() {
        $rule = new Rule(null, [
            'context' => ['!invalid']
        ]);

        $this->assertTrue($rule->validateContext([]));
        $this->assertTrue($rule->validateContext(['some']));
        $this->assertFalse($rule->validateContext(['invalid']));
    }

    public function testInOneOfContextValidation() {
        $rule = new Rule(null, [
            'context' => ['*first', '*second']
        ]);

        $this->assertTrue($rule->validateContext(['first']));
        $this->assertTrue($rule->validateContext(['second']));
        $this->assertFalse($rule->validateContext([]));
        $this->assertFalse($rule->validateContext(['other']));
    }

    public function testSubTokens() {
        $rule = new Rule(null, [
            'context' => ['token.test', '!token']
        ]);

        $this->assertTrue($rule->validateContext(['token.test']));
        $this->assertFalse($rule->validateContext(['token']));
    }

    public function testInAll() {
        $rule = new Rule(null, [
            'context' => ['!!']
        ]);

        $this->assertTrue($rule->validateContext(['first']));
        $this->assertTrue($rule->validateContext(['second']));
        $this->assertTrue($rule->validateContext([]));
        $this->assertTrue($rule->validateContext(['other']));
    }

    public function testInNone() {
        $rule = new Rule(null);

        $this->assertFalse($rule->validateContext(['first']));
        $this->assertFalse($rule->validateContext(['second']));
        $this->assertTrue($rule->validateContext([]));
        $this->assertFalse($rule->validateContext(['other']));
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
