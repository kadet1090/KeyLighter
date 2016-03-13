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


use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Parser\CloseRule;
use Kadet\Highlighter\Parser\OpenRule;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token;

class TokenTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $rule  = new Rule();
        $token = new Token(['test.name', 'pos' => 10, 'index' => 10, 'rule' => $rule]);

        $this->assertEquals($token->name, 'test.name', 'Token name is invalid');
        $this->assertEquals($token->pos, 10, 'Position is invalid');
        $this->assertEquals($token->index, 10, 'Index is invalid');
        $this->assertEquals($token->getRule(), $rule, 'Rule is invalid');
    }

    public function testCreationWithStart()
    {
        $start = new Token(['test.name', 'pos' => 5]);
        $token = new Token(['test.name', 'pos' => 10, 'start' => $start]);

        $this->assertEquals($token->getStart(), $start, 'Token is not pointing to start.');
        $this->assertEquals($start->getEnd(), $token, 'Start is not pointing to token.');
    }

    public function testCreationWithEnd()
    {
        $end   = new Token(['test.name', 'pos' => 15]);
        $token = new Token(['test.name', 'pos' => 10, 'end' => $end]);

        $this->assertEquals($token->getEnd(), $end, 'Token is not pointing to end.');
        $this->assertEquals($end->getStart(), $token, 'End is not pointing to token.');
    }

    public function testCreationWithLength()
    {
        $token = new Token(['test.name', 'pos' => 15, 'length' => 10]);

        $this->assertEquals($token->getEnd()->pos, 25, 'Token is not pointing to end.');
    }

    public function testLength()
    {
        $token = new Token(['test.name', 'pos' => 15, 'length' => 10]);

        $this->assertEquals($token->getLength(), 10, 'Length is invalid');
    }

    public function testIsStart()
    {
        $token = new Token(['test.name', 'pos' => 10]);
        $close = new Token(['test.name', 'pos' => 10, 'rule' => new CloseRule()]);

        $this->assertTrue($token->isStart());
        $this->assertFalse($close->isStart());
    }

    public function testIsEnd()
    {
        $token = new Token(['test.name', 'pos' => 15]);
        new Token(['test.name', 'pos' => 10, 'end' => $token]);

        $close = new Token(['test.name', 'pos' => 10, 'rule' => new OpenRule()]);

        $this->assertTrue($token->isEnd());
        $this->assertFalse($close->isEnd());
    }

    public function testInvalidation() {
        /** @var Language $language */
        $language = $this->getMock('Kadet\Highlighter\Language\Language');

        $token = new Token(['test.name', 'pos' => 15, 'length' => 10]);

        $token->setValid(false);
        $this->assertFalse($token->isValid($language));
        $this->assertFalse($token->getEnd()->isValid($language));

        $token->getEnd()->setValid(true);
        $this->assertTrue($token->isValid($language));
        $this->assertTrue($token->getEnd()->isValid($language));
    }

    public function testTokenValidation() {
        /** @var Language $language */
        $language = $this->getMock('Kadet\Highlighter\Language\Language');

        $token = new Token(['test.name', 'pos' => 15, 'length' => 10, 'rule' => new Rule(null, ['language' => $language])]);
        $this->assertTrue($token->isValid($language, []));
    }
}
