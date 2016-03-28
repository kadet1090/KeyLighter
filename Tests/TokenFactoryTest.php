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

use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\TokenFactory;

class TokenFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testTokenCreation()
    {
        $factory = new TokenFactory(Token::class);
        $this->assertInstanceOf(Token::class, $factory->create([]));
    }

    public function testRuleInheriting()
    {
        $rule = $this->getMockBuilder('Kadet\Highlighter\Parser\Rule')->disableOriginalConstructor()->getMock();

        $factory = new TokenFactory(Token::class);
        $factory->setRule($rule);

        $token = $factory->create([]);
        $this->assertEquals($rule, $token->rule);
    }

    public function testSubNaming()
    {
        $factory = new TokenFactory(Token::class);
        $factory->setBase('token');

        $this->assertEquals('token.name', $factory->create(['$.name'])->name);
    }

    public function testOffset()
    {
        $factory = new TokenFactory(Token::class);
        $factory->setOffset(10);
        $this->assertEquals(15, $factory->create(['token', 'pos' => 5])->pos);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWrongClass()
    {
        new TokenFactory('wrong-class');
    }
}
