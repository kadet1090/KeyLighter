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

namespace Kadet\KeyLighter\Tests;


use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Parser\MarkerToken;
use Kadet\Highlighter\Parser\Rule;

class MarkerTokenTest extends \PHPUnit_Framework_TestCase
{
    public function testValidation() {
        /** @var Language $lang */
        $lang = $this->getMock('Kadet\Highlighter\Language\Language');
        $rule = new Rule(null, ['language' => $lang]);

        $start    = new MarkerToken(['test', 'pos' => 10, 'length' => 1, 'rule' => $rule]);
        $startEnd = $start->getEnd();

        $endStart = new MarkerToken(['test', 'pos' => 12, 'length' => 1, 'rule' => $rule]);
        $end      = $endStart->getEnd();

        $this->assertTrue($start->isValid($lang, []));
        $this->assertFalse($startEnd->isValid($lang, ['test']));
        $this->assertFalse($endStart->isValid($lang, ['test']));
        $this->assertTrue($end->isValid($lang, ['test']));

        $this->assertTrue($start->isStart());
        $this->assertTrue($endStart->isStart());
        $this->assertTrue($end->isEnd());
        $this->assertTrue($startEnd->isEnd());
    }

    public function testInvalid() {
        /** @var Language $lang */
        $lang = $this->getMock('Kadet\Highlighter\Language\Language');
        $rule = new Rule(null, ['language' => $lang]);

        $start    = new MarkerToken(['test', 'pos' => 10, 'length' => 1, 'rule' => $rule]);
        $startEnd = $start->getEnd();

        $endStart = new MarkerToken(['test', 'pos' => 12, 'length' => 1, 'rule' => $rule]);
        $end      = $endStart->getEnd();

        /** @noinspection PhpParamsInspection */
        $this->assertFalse($start->isValid($this->getMock('Kadet\Highlighter\Language\Language'), ['nope']));
        $this->assertFalse($startEnd->isValid($lang, ['nope']));

        $this->assertFalse($endStart->isValid($lang, ['nope']));
        $this->assertFalse($end->isValid($lang, ['nope']));
    }
}
