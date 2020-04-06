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
use Kadet\Highlighter\Parser\Result;
use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\TokenFactory;
use PHPUnit\Framework\TestCase;

class HtmlFormatterTest extends TestCase
{
    /**
     * @var TokenFactory
     */
    private $_factory;

    public function setUp()
    {
        $this->_factory = new TokenFactory(Token::class);
    }

    public function testRender()
    {
        $source   = 'abc + test';
        $expected = <<<EXPECTED
<span class="kl-token">abc</span> <span class="kl-operator">+</span> <span class="kl-token kl-second">test</span>
EXPECTED;

        $first    = $this->_factory->create('token', ['pos' => 0, 'length' => 3]);
        $operator = $this->_factory->create('operator', ['pos' => 4, 'length' => 1]);
        $second   = $this->_factory->create('token.second', ['pos' => 6, 'length' => 4]);

        $iterator = new Result($source);
        $iterator->merge([
            $first, $first->getEnd(),
            $operator, $operator->getEnd(),
            $second, $second->getEnd()
        ]);

        $formatter = new HtmlFormatter();
        $this->assertEquals($expected, $formatter->format($iterator));
    }
}
