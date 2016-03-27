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

use Kadet\Highlighter\Matcher\WholeMatcher;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\Validator\DelegateValidator;

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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsOnWrongValidator() {
        new Rule(null, [
            'context' => 'nope'
        ]);
    }

    public function testAcceptsCallableAsContext() {
        $rule = new Rule(null, [
            'context' => function() { return true; }
        ]);

        $this->assertInstanceOf(DelegateValidator::class, $rule->validator);
    }

    public function testDisable() {
        $rule = new Rule(new WholeMatcher());

        $rule->disable();
        $this->assertEmpty($rule->match('source'));
    }

    public function testEnable() {
        $rule = new Rule(new WholeMatcher(), ['enabled' => false]);

        $rule->enable();
        $this->assertNotEmpty($rule->match('source'));
    }
}
