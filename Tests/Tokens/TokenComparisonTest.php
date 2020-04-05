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


use Kadet\Highlighter\Parser\Token\Token;
use PHPUnit\Framework\TestCase;

class TokenComparisonTest extends TestCase
{
    public function testStartSucceedsEnd()
    {
        $start = new Token('name', ['pos' => 1]);
        $end   = new Token('name', ['pos' => 1]);

        $start->setEnd($end);

        $this->assertEquals(-1, Token::compare($start, $end));
        $this->assertEquals( 1, Token::compare($end, $start));
    }
}
