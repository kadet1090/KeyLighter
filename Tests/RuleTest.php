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
        $language = $this
            ->getMockBuilder('Kadet\Highlighter\Language\Language')
            ->disableOriginalConstructor()
            ->getMock();

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
