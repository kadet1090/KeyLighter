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


use Kadet\Highlighter\Parser\TokenFactory;

class TokenFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testTokenCreation() {
        $factory = new TokenFactory('Kadet\Highlighter\Parser\Token');
        $this->assertInstanceOf('Kadet\Highlighter\Parser\Token', $factory->create([]));
    }

    public function testRuleInheriting() {
        $rule = $this->getMockBuilder('Kadet\Highlighter\Parser\Rule')->disableOriginalConstructor()->getMock();

        $factory = new TokenFactory('Kadet\Highlighter\Parser\Token');
        $factory->setRule($rule);

        $token = $factory->create([]);
        $this->assertEquals($rule, $factory->getRule());
        $this->assertEquals($rule, $token->getRule());
    }

    public function testGettingClass() {
        $factory = new TokenFactory('Kadet\Highlighter\Parser\Token');
        $this->assertEquals('Kadet\Highlighter\Parser\Token', $factory->getClass());
    }

    public function testSubNaming() {
        $factory = new TokenFactory('Kadet\Highlighter\Parser\Token');
        $factory->setBase('token');

        $this->assertEquals('token.name', $factory->create(['$.name'])->name);
        $this->assertEquals('token', $factory->getBase());
    }

    public function testOffset() {
        $factory = new TokenFactory('Kadet\Highlighter\Parser\Token');
        $factory->setOffset(10);
        $this->assertEquals(15, $factory->create(['token', 'pos' => 5])->pos);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWrongClass() {
        new TokenFactory('wrong-class');
    }
}
