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
use Kadet\Highlighter\Parser\Context;
use Kadet\Highlighter\Parser\Token\Token;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{
    public function testLanguageBinding()
    {
        $language = $this->getLanguageMock();
        $context = new Context($language);

        $this->assertSame($language, $context->language);
        $this->assertEmpty($context->stack);
    }

    public function testCreationFromArray()
    {
        $array = ['token', 'test'];
        $language = $this->getLanguageMock();

        $context = Context::fromArray($array, $language);

        $this->assertSame($language, $context->language);
        $this->assertEquals($array, $context->stack);
    }

    public function testPush()
    {
        $context = new Context();
        $context->push(new Token('token.name'));

        $this->assertSame(['token.name'], array_values($context->stack));
    }

    public function testPop()
    {
        $foo = $this->getTokenMock('foo');
        $bar = $this->getTokenMock('bar');

        $context = new Context();
        $context->push($foo);
        $context->push($bar);

        $context->pop($foo);
        $this->assertSame(['bar'], array_values($context->stack));

        $context->pop($bar);
        $this->assertEmpty($context->stack);
    }

    public function testHas()
    {
        $context = new Context();
        $this->assertFalse($context->has('token.name'));

        $context->push($this->getTokenMock('token.name'));
        $this->assertTrue($context->has('token.name'));
    }

    public function testFind()
    {
        $token = $this->getTokenMock('token.name');
        $context = new Context();

        $context->push($token);
        $this->assertSame($token->id, $context->find('token.name'));
        $this->assertFalse($context->find('foo'));
    }
    
    public function getLanguageMock() {
        return $this->getMockBuilder(Language::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
    }

    /**
     * @param $name
     * @param $params
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|Token
     */
    public function getTokenMock($name, $params = []) {
        return $this->getMockBuilder(Token::class)
            ->setConstructorArgs([$name, $params])
            ->getMock();
    }
}
