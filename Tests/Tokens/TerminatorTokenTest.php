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

namespace Kadet\Highlighter\Tests\Tokens;


use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\TerminatorToken;
use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\TokenIterator;
use Kadet\Highlighter\Parser\Validator\Validator;

class TerminatorTokenTest extends TokenTestCase
{
    /**
     * @uses Kadet\Highlighter\Parser\Context
     * @uses Kadet\Highlighter\Parser\TokenIterator
     */
    public function testProcessStart()
    {
        $token = new TerminatorToken('test');
        $token->setEnd(false);
        $token->setValid(true);

        $iterator = new TokenIterator([$token->id => $token], '');

        $this->_result->expects($this->never())->method('append')->withAnyParameters();

        $token->process($this->_context, $this->_language, $this->_result, $iterator);

        $this->assertEmpty($this->_context->stack);
    }

    public function testProcessEnd()
    {
        $foo = $this->getMockBuilder(Token::class)->setConstructorArgs(['foo'])->getMock();
        $bar = $this->getMockBuilder(Token::class)->setConstructorArgs(['bar'])->getMock();

        $this->_context->push($foo);
        $this->_context->push($bar);

        $foo->expects($this->once())->method('setEnd')->with($this->isInstanceOf(Token::class));
        $bar->expects($this->never())->method('setEnd')->withAnyParameters();

        $token = new TerminatorToken('test', ['rule' => new Rule(null, ['closes' => ['foo']])]);
        $token->setStart(false);
        $token->setValid(true);

        $iterator = new TokenIterator([
            $token->id => $token,
            $foo->id => $foo,
            $bar->id => $bar,
        ], '');

        $token->process($this->_context, $this->_language, $this->_result, $iterator);
        $this->assertEquals(['bar'], array_values($this->_context->stack));
    }

    public function testValid()
    {
        $validator = $this->createMock(Validator::class);
        $validator->expects($this->once())->method('validate')->with($this->_context, [])->willReturn(true);

        $rule = new Rule(null, ['language' => $this->_language, 'context' => $validator]);

        $token = new TerminatorToken('test', ['rule' => $rule]);
        $token->setStart(false);

        $this->assertTrue($token->isValid($this->_context));
    }

    public function testInvalid()
    {

        $rule = new Rule(null, ['language' => false]);

        $token = new TerminatorToken('test', ['rule' => $rule]);
        $token->setStart(false);

        $this->assertFalse($token->isValid($this->_context));
    }
}
