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

use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Parser\Context;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\Highlighter\Parser\Validator\Validator;

class TokenTest extends TokenTestCase
{

    /**
     * @var TokenFactory
     */
    private $_factory;

    public function setUp()
    {
        $this->_factory = new TokenFactory(Token::class);
    }

    public function testCreation()
    {
        $rule  = new Rule();
        $token = $this->_factory->create('test.name', ['pos' => 10, 'rule' => $rule]);

        $this->assertEquals($token->name, 'test.name', 'Token name is invalid');
        $this->assertEquals($token->pos, 10, 'Position is invalid');
        $this->assertEquals($token->rule, $rule, 'Rule is invalid');
    }

    public function testCreationWithStart()
    {
        $start = $this->_factory->create('test.name', ['pos' => 5]);
        $end   = $this->_factory->create('test.name', ['test.name', 'pos' => 10, 'start' => $start])->getEnd();

        $this->assertEquals($end->getStart(), $start, 'Token is not pointing to start.');
        $this->assertEquals($start->getEnd(), $end, 'Start is not pointing to token.');
    }

    public function testCreationWithEnd()
    {
        $end   = $this->_factory->create('test.name', ['pos' => 15]);
        $token = $this->_factory->create('test.name', ['pos' => 10, 'end' => $end]);

        $this->assertEquals($token->getEnd(), $end, 'Token is not pointing to end.');
        $this->assertEquals($end->getStart(), $token, 'End is not pointing to token.');
    }

    public function testCreationWithLength()
    {
        $token = $this->_factory->create('test.name', ['pos' => 15, 'length' => 10]);

        $this->assertEquals($token->getEnd()->pos, 25, 'Token is not pointing to end.');
    }

    public function testLength()
    {
        $token = $this->_factory->create('test.name', ['test.name', 'pos' => 15, 'length' => 10]);

        $this->assertEquals($token->getLength(), 10, 'Length is invalid');
    }

    public function testInvalidation()
    {
        /** @var Language $language */
        $language = $this
            ->getMockBuilder('Kadet\Highlighter\Language\Language')
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->_factory->create('test.name', ['pos' => 15, 'length' => 10]);

        $token->setValid(false);
        $this->assertFalse($token->isValid(Context::fromArray([], $language)));
        $this->assertFalse($token->getEnd()->isValid(Context::fromArray([], $language)));

        $token->getEnd()->setValid(true);
        $this->assertTrue($token->isValid(Context::fromArray([], $language)));
        $this->assertTrue($token->getEnd()->isValid(Context::fromArray([], $language)));
    }

    public function testTokenValidation()
    {
        /** @var Language $language */
        $language = $this
            ->getMockBuilder('Kadet\Highlighter\Language\Language')
            ->disableOriginalConstructor()
            ->getMock();

        $validator = $this->createMock(Validator::class);
        $context   = Context::fromArray([], $language);

        $validator->expects($this->once())->method('validate')->with($context, []);

        $token = $this->_factory->create(
            'test.name',
            [
                'pos'    => 15,
                'length' => 10,
                'rule'   => new Rule(
                    null,
                    [
                    'language' => $language,
                    'context'  => $validator
                    ]
                )
            ]
        );
        $token->isValid($context);
    }
}
