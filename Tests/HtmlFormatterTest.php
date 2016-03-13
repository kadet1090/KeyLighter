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


use Kadet\Highlighter\Formatter\HtmlFormatter;
use Kadet\Highlighter\Parser\Token;
use Kadet\Highlighter\Parser\TokenIterator;

class HtmlFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testRender() {
        $source = 'abc + test';
        $expected = <<<EXPECTED
<span class="token">abc</span> <span class="operator">+</span> <span class="token second">test</span>
EXPECTED;

        $first    = new Token(['token', 'pos' => 0, 'length' => 3]);
        $operator = new Token(['operator', 'pos' => 4, 'length' => 1]);
        $second   = new Token(['token.second', 'pos' => 6, 'length' => 4]);

        $iterator = new TokenIterator([
            $first, $first->getEnd(),
            $operator, $operator->getEnd(),
            $second, $second->getEnd()
        ], $source);

        $formatter = new HtmlFormatter();
        $this->assertEquals($expected, $formatter->format($iterator));
    }
}
