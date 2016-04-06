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
use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\Highlighter\Parser\UnprocessedTokens;

class TokenListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TokenFactory
     */
    private $_factory;

    public function setUp()
    {
        $this->_factory = new TokenFactory(Token::class);
    }

    public function testTokenSortingPositions()
    {
        $tokens = [
            $this->_factory->create(null, ['token.name', 'pos' => 25]),
            $this->_factory->create(null, ['token.name', 'pos' => 15]),
        ];

        $list = new UnprocessedTokens();
        $list->batch($tokens);
        $list->sort();

        $this->assertEquals(array_reverse($tokens), array_values($list->toArray()));
    }

    public function testTokenSortingPriority()
    {
        $tokens    = [];
        $tokens[0] = $token = $this->_factory->create(
            null, ['token.1', 'pos' => 2, 'length' => 3, 'rule' => new Rule(null, ['priority' => 3])]
        );
        $tokens[1] = $token->getEnd();
        $tokens[2] = $token = $this->_factory->create(
            null, ['token.2', 'pos' => 2, 'length' => 3, 'rule' => new Rule(null, ['priority' => 1])]
        );
        $tokens[3] = $token->getEnd();
        $tokens[4] = $token = $this->_factory->create(
            null, ['token.3', 'pos' => 2, 'length' => 4, 'rule' => new Rule(null, ['priority' => 2])]
        );
        $tokens[5] = $token->getEnd();

        $list = new UnprocessedTokens();
        $list->add($tokens[0]);
        $list->add($tokens[2]);
        $list->add($tokens[4]);
        $list->sort();

        $this->assertEquals(
            [$tokens[0], $tokens[4], $tokens[2], $tokens[3], $tokens[1], $tokens[5]],
            array_values($list->toArray())
        );
    }

    public function testTokenSortingIndex()
    {
        $tokens    = [];
        $tokens[0] = $token = $this->_factory->create(null, ['token.1', 'pos' => 2, 'length' => 3, 'index' => 3]);
        $tokens[1] = $token->getEnd();
        $tokens[2] = $token = $this->_factory->create(null, ['token.2', 'pos' => 2, 'length' => 3, 'index' => 1]);
        $tokens[3] = $token->getEnd();
        $tokens[4] = $token = $this->_factory->create(null, ['token.3', 'pos' => 2, 'length' => 4, 'index' => 2]);
        $tokens[5] = $token->getEnd();

        $list = new UnprocessedTokens();
        $list->add($tokens[0]);
        $list->add($tokens[2]);
        $list->add($tokens[4]);
        $list->sort();

        $this->assertEquals(
            [$tokens[0], $tokens[4], $tokens[2], $tokens[3], $tokens[1], $tokens[5]],
            array_values($list->toArray())
        );
    }

    public function testTokenSortingFallback()
    {
        $tokens    = [];
        $tokens[0] = $token = $this->_factory->create(null, ['token.1', 'pos' => 2, 'length' => 3]);
        $tokens[1] = $token->getEnd();
        $tokens[2] = $token = $this->_factory->create(null, ['token.2', 'pos' => 2, 'length' => 3]);
        $tokens[3] = $token->getEnd();
        $tokens[4] = $token = $this->_factory->create(null, ['token.3', 'pos' => 2, 'length' => 4]);
        $tokens[5] = $token->getEnd();

        $list = new UnprocessedTokens();
        $list->add($tokens[0]);
        $list->add($tokens[2]);
        $list->add($tokens[4]);
        $list->sort();

        $this->assertEquals(
            [$tokens[0], $tokens[2], $tokens[4], $tokens[3], $tokens[1], $tokens[5]],
            array_values($list->toArray())
        );
    }

    public function testTokenSortingEndProceedsStart()
    {
        $tokens    = [];
        $tokens[0] = $token = $this->_factory->create(null, ['token.1', 'pos' => 2, 'length' => 0]);
        $tokens[1] = $token->getEnd();
        $tokens[2] = $token = $this->_factory->create(null, ['token.3', 'pos' => 5, 'length' => 4]);
        $tokens[3] = $token->getEnd();
        $tokens[4] = $token = $this->_factory->create(null, ['token.2', 'pos' => 2, 'length' => 3]);
        $tokens[5] = $token->getEnd();

        $list = new UnprocessedTokens();
        $list->add($tokens[0]);
        $list->add($tokens[2]);
        $list->add($tokens[4]);
        $list->sort();

        $this->assertEquals(
            [$tokens[0], $tokens[1], $tokens[4], $tokens[5], $tokens[2], $tokens[3]],
            array_values($list->toArray())
        );
    }
}
