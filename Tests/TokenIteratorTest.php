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


use Kadet\Highlighter\Parser\Token;
use Kadet\Highlighter\Parser\TokenIterator;

class TokenIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testSourceReturning() {
        $iterator = new TokenIterator([], 'source');

        $this->assertEquals('source', $iterator->getSource());
    }

    public function testTokenReturning() {
        $tokens = [
            new Token(['token.name', 'pos' => 15]),
            new Token(['token.name', 'pos' => 25]),
        ];

        $iterator = new TokenIterator($tokens, 'source');

        $this->assertEquals($tokens, $iterator->getTokens());
    }
}
