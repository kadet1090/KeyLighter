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

use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Parser\Context;
use Kadet\Highlighter\Parser\Token\ContextualToken;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\TokenFactory;

class ContextualTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TokenFactory
     */
    private $_factory;

    public function setUp()
    {
        $this->_factory = new TokenFactory(ContextualToken::class);
    }

    private function getLanguageMock() {
        return $this->getMockBuilder(Language::class)->disableOriginalConstructor()->getMock();
    }
    
    public function testClose()
    {
        /** @var Language $lang */
        $lang = $this->getLanguageMock();
        $rule = new Rule(null, ['language' => $lang, 'context' => [
            '!nope'
        ]]);

        $start    = $this->_factory->create(['test', 'pos' => 10, 'length' => 1, 'rule' => $rule]);
        $startEnd = $start->getEnd();

        $endStart = $this->_factory->create(['test', 'pos' => 12, 'length' => 1, 'rule' => $rule]);
        $end      = $endStart->getEnd();

        /** @noinspection PhpParamsInspection */
        $this->assertFalse($start->isValid(Context::fromArray(['nope'], $this->getLanguageMock())));
        $this->assertFalse($startEnd->isValid(Context::fromArray(['nope'], $lang)));

        $this->assertFalse($endStart->isValid(Context::fromArray(['test', 'nope'], $lang)));
        $this->assertFalse($end->isValid(Context::fromArray(['test', 'nope'], $lang)));
    }
}
