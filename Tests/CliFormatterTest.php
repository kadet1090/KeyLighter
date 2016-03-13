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


use Kadet\Highlighter\Formatter\CliFormatter;
use Kadet\Highlighter\Parser\Token;
use Kadet\Highlighter\Parser\TokenIterator;
use Kadet\Highlighter\Utils\Console;

class CliFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testRendering() {
        $source = 'abc + test';
        $expected = Console::open(['color' => 'red']).'abc'.Console::close().' '.
            Console::open(['color' => 'blue']).'+'.Console::close().' '.
            Console::open(['color' => 'red']).'test'.Console::close().Console::reset();

        $first    = new Token(['token', 'pos' => 0, 'length' => 3]);
        $operator = new Token(['operator', 'pos' => 4, 'length' => 1]);
        $second   = new Token(['token.second', 'pos' => 6, 'length' => 4]);

        $iterator = new TokenIterator([
            $first, $first->getEnd(),
            $operator, $operator->getEnd(),
            $second, $second->getEnd()
        ], $source);

        $formatter = new CliFormatter([
           'token' => ['color' => 'red'],
           'operator' => ['color' => 'blue'],
        ]);
        $this->assertEquals($expected, $formatter->format($iterator));
    }
}
