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

use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\TokenIterator;
use PHPUnit\Framework\TestCase;

class TokenIteratorTest extends TestCase
{
    public function testSourceReturning()
    {
        $iterator = new TokenIterator([], 'source');

        $this->assertEquals('source', $iterator->getSource());
    }

    public function testTokenReturning()
    {
        $tokens = [
            new Token(null, ['token.name', 'pos' => 15]),
            new Token(null, ['token.name', 'pos' => 25]),
        ];

        $iterator = new TokenIterator($tokens, 'source');

        $this->assertEquals($tokens, $iterator->getTokens());
    }
}
