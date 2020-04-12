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

namespace Kadet\Highlighter\Tests\Tokens;

use Kadet\Highlighter\Language\GreedyLanguage;
use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Parser\Context;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\ContextualToken;
use Kadet\Highlighter\Parser\TokenFactory;
use PHPUnit\Framework\TestCase;

class ContextualTokenTest extends TestCase
{
    /**
     * @var TokenFactory
     */
    private $_factory;
    private $_language;
    private $_rule;

    public function setUp()
    {
        $this->_factory  = new TokenFactory(ContextualToken::class);
        $this->_language = $this->getLanguageMock();
        $this->_rule     = new Rule(null, [
            'language' => $this->_language,
            'context'  => [
                '!nope',
            ],
        ]);
    }

    public function testClose()
    {
        $start    = $this->_factory->create('test', ['pos' => 10, 'length' => 1, 'rule' => $this->_rule]);
        $startEnd = $start->getEnd();

        $endStart = $this->_factory->create('test', ['pos' => 12, 'length' => 1, 'rule' => $this->_rule]);
        $end      = $endStart->getEnd();

        /** @noinspection PhpParamsInspection */
        $this->assertFalse($start->isValid(Context::fromArray(['nope'], $this->_language)));
        $this->assertFalse($startEnd->isValid(Context::fromArray(['nope'], $this->_language)));

        $this->assertFalse($endStart->isValid(Context::fromArray(['test', 'nope'], $this->_language)));
        $this->assertFalse($end->isValid(Context::fromArray(['test', 'nope'], $this->_language)));
    }

    public function testInvalidLanguage()
    {
        $start    = $this->_factory->create('test', ['pos' => 10, 'length' => 1, 'rule' => $this->_rule]);
        $startEnd = $start->getEnd();

        $this->assertFalse($start->isValid(Context::fromArray(['nope'], $this->getLanguageMock())));
        $this->assertFalse($startEnd->isValid(Context::fromArray(['nope'], $this->_language)));
    }

    public function testValidation()
    {
        $start    = $this->_factory->create('test', ['pos' => 10, 'length' => 1, 'rule' => $this->_rule]);
        $startEnd = $start->getEnd();

        $endStart = $this->_factory->create('test', ['pos' => 12, 'length' => 1, 'rule' => $this->_rule]);
        $end      = $endStart->getEnd();

        $this->assertTrue($start->isValid(Context::fromArray([], $this->_language)));
        $this->assertFalse($startEnd->isValid(Context::fromArray(['test'], $this->_language)));
        $this->assertFalse($endStart->isValid(Context::fromArray(['test'], $this->_language)));
        $this->assertTrue($end->isValid(Context::fromArray(['test'], $this->_language)));

        $this->assertTrue($start->isStart());
        $this->assertTrue($endStart->isStart());
        $this->assertTrue($end->isEnd());
        $this->assertTrue($startEnd->isEnd());
    }

    /** @return Language|\PHPUnit_Framework_MockObject_MockObject */
    private function getLanguageMock()
    {
        return $this->getMockBuilder(GreedyLanguage::class)->disableOriginalConstructor()->getMock();
    }
}
