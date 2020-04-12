<?php

declare(strict_types=1);

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

use Kadet\Highlighter\Parser\DelegateTokenFactory;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\TokenFactoryInterface;
use PHPUnit\Framework\TestCase;

class DelegateTokenFactoryTest extends TestCase
{
    public function testCreation()
    {
        $params = ['token.name', 'pos' => 3];
        $child = $this->getMockBuilder(TokenFactoryInterface::class)->getMock();

        $mock = $this->createPartialMock('stdClass', ['call']);
        $mock->expects($this->once())->method('call')->with($child, $params);

        $factory = new DelegateTokenFactory([$mock, 'call'], $child);
        $factory->create(null, $params);
    }

    public function testMethodDelegation()
    {
        $rule = $this->getMockBuilder(Rule::class)->disableOriginalConstructor()->getMock();

        $child = $this->getMockBuilder(TokenFactoryInterface::class)->getMock();

        $child->expects($this->once())->method('setBase')->with('token');
        $child->expects($this->once())->method('setClass')->with(Token::class);
        $child->expects($this->once())->method('setRule')->with($rule);
        $child->expects($this->once())->method('setType')->with(3);

        $factory = new DelegateTokenFactory(function () {
        }, $child);
        $factory->setBase('token');
        $factory->setClass(Token::class);
        $factory->setRule($rule);
        $factory->setType(3);
    }
}
