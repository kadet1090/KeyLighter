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

use Kadet\Highlighter\Formatter\CliFormatter;
use Kadet\Highlighter\Parser\Result;
use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\Highlighter\Utils\Console;
use PHPUnit\Framework\TestCase;

class CliFormatterTest extends TestCase
{
    /**
     * @var TokenFactory
     */
    private $_factory;

    public function setUp(): void
    {
        $this->_factory = new TokenFactory(Token::class);
    }

    public function testRendering()
    {
        $source   = 'abc + test';
        $expected = Console::open(['color' => 'red']) . 'abc' . Console::close() . ' ' .
            Console::open(['color' => 'blue']) . '+' . Console::close() . ' ' .
            Console::open(['color' => 'red']) . 'test' . Console::close() . Console::reset();

        $first    = $this->_factory->create('token', ['pos' => 0, 'length' => 3]);
        $operator = $this->_factory->create('operator', ['pos' => 4, 'length' => 1]);
        $second   = $this->_factory->create('token.second', ['pos' => 6, 'length' => 4]);

        $iterator = new Result($source);
        $iterator->merge([
            $first, $first->getEnd(),
            $operator, $operator->getEnd(),
            $second, $second->getEnd()
        ]);

        $formatter = new CliFormatter(['styles' => [
           'token'    => ['color' => 'red'],
           'operator' => ['color' => 'blue'],
        ]]);
        $this->assertEquals($expected, $formatter->format($iterator));
    }

    public function testCallable()
    {
        $this->markTestSkipped('This test requires callback mocks, that are not yet implemented.');

        $source   = 'abc';
        $expected = Console::open(['color' => 'red']) . 'abc' . Console::close() . Console::reset();

        $token    = $this->_factory->create('token', ['pos' => 0, 'length' => 3]);

        $iterator = new Result($source);
        $iterator->merge([
            $token, $token->getEnd(),
        ]);

        $mock = $this->createPartialMock('stdClass', ['call']);
        $mock->expects($this->once())->method('call')->with($token)->willReturn(['color' => 'red']);

        $formatter = new CliFormatter(['styles' => [
            'token'    => [$mock, 'call']
        ]]);
        $this->assertEquals($expected, $formatter->format($iterator));
    }
}
