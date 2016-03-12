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


use Kadet\Highlighter\Parser\Rule;
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

    public function testTokenSortingPositions() {
        $tokens = [
            new Token(['token.name', 'pos' => 25]),
            new Token(['token.name', 'pos' => 15]),
        ];

        $iterator = new TokenIterator($tokens, 'source');
        $iterator->sort();

        $this->assertEquals(array_reverse($tokens, true), $iterator->getTokens());
    }

    public function testTokenSortingPriority() {
        $tokens = [];
        $tokens[0] = $token = new Token(['token.1', 'pos' => 2, 'length' => 3, 'rule' => new Rule(null, ['priority' => 3])]);
        $tokens[1] = $token->getEnd();
        $tokens[2] = $token = new Token(['token.2', 'pos' => 2, 'length' => 3, 'rule' => new Rule(null, ['priority' => 1])]);
        $tokens[3] = $token->getEnd();
        $tokens[4] = $token = new Token(['token.3', 'pos' => 2, 'length' => 4, 'rule' => new Rule(null, ['priority' => 2])]);
        $tokens[5] = $token->getEnd();

        $iterator = new TokenIterator($tokens, 'source');
        $iterator->sort();

        $this->assertEquals(
            [0, 4, 2, 3, 1, 5],
            array_keys($iterator->getTokens())
        );
    }

    public function testTokenSortingIndex() {
        $tokens = [];
        $tokens[0] = $token = new Token(['token.1', 'pos' => 2, 'length' => 3, 'index' => 3]);
        $tokens[1] = $token->getEnd();
        $tokens[2] = $token = new Token(['token.2', 'pos' => 2, 'length' => 3, 'index' => 1]);
        $tokens[3] = $token->getEnd();
        $tokens[4] = $token = new Token(['token.3', 'pos' => 2, 'length' => 4, 'index' => 2]);
        $tokens[5] = $token->getEnd();

        $iterator = new TokenIterator($tokens, 'source');
        $iterator->sort();

        $this->assertEquals(
            [0, 4, 2, 3, 1, 5],
            array_keys($iterator->getTokens())
        );
    }

    public function testTokenSortingFallback() {
        $tokens = [];
        $tokens[0] = $token = new Token(['token.1', 'pos' => 2, 'length' => 3]);
        $tokens[1] = $token->getEnd();
        $tokens[2] = $token = new Token(['token.2', 'pos' => 2, 'length' => 3]);
        $tokens[3] = $token->getEnd();
        $tokens[4] = $token = new Token(['token.3', 'pos' => 2, 'length' => 4]);
        $tokens[5] = $token->getEnd();

        $iterator = new TokenIterator($tokens, 'source');
        $iterator->sort();

        $this->assertEquals(
            [0, 2, 4, 3, 1, 5],
            array_keys($iterator->getTokens())
        );
    }

    public function testTokenSortingEndProceedsStart() {
        $tokens = [];
        $tokens[0] = $token = new Token(['token.1', 'pos' => 2, 'length' => 0]);
        $tokens[1] = $token->getEnd();
        $tokens[2] = $token = new Token(['token.3', 'pos' => 5, 'length' => 4]);
        $tokens[3] = $token->getEnd();
        $tokens[4] = $token = new Token(['token.2', 'pos' => 2, 'length' => 3]);
        $tokens[5] = $token->getEnd();

        $iterator = new TokenIterator($tokens, 'source');
        $iterator->sort();

        $this->assertEquals(
            [0, 1, 4, 5, 2, 3],
            array_keys($iterator->getTokens())
        );
    }
}
