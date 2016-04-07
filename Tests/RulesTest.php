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


use Kadet\Highlighter\Language\GreedyLanguage;
use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Rules;

class RulesTest extends \PHPUnit_Framework_TestCase
{
    public function testAddsRule()
    {
        $rule = new Rule();

        $rules = new Rules($this->getLanguageMock());
        $rules->add('test',  $rule);
        $rules->add('token', $rule);

        $this->assertContains($rule, $rules->all());
        $this->assertContainsOnlyInstancesOf(Rule::class, $rules->all());
    }

    public function testAddsMany()
    {
        $rule = new Rule();

        $rules = new Rules($this->getLanguageMock());
        $rules->addMany([
            'token' => $rule,
            'test'  => [
                'embedded' => $rule,
                $rule
            ]
        ]);

        $this->assertArrayHasKey('token', $rules);
        $this->assertArrayHasKey('test', $rules);
        $this->assertArrayHasKey('test.embedded', $rules);
    }

    public function testRemove()
    {
        $rule = new Rule();

        $rules = new Rules($this->getLanguageMock());
        $rules->addMany([
            'token' => $rule,
            'test'  => [
                'embedded' => $rule,
                $rule,
                $rule
            ]
        ]);
        $rules->remove('test.embedded');
        $rules->remove('test', 0);

        $this->assertArrayHasKey('token', $rules);
        $this->assertArrayHasKey('test', $rules);
        $this->assertArrayNotHasKey('test.embedded', $rules);

        $this->assertCount(1, $rules['test']);
    }

    public function testReturning()
    {
        $rule = new Rule();

        $rules = new Rules($this->getLanguageMock());
        $rules->addMany([
            'token' => $rule,
            'test'  => [
                'embedded' => $rule,
                $rule
            ]
        ]);

        $this->assertInternalType('array', $rules->rules('token'));
        $this->assertContainsOnlyInstancesOf(Rule::class, $rules->rules('token'));
        $this->assertEquals($rule, $rules->rule('token'));
    }

    /** @expectedException \InvalidArgumentException */
    public function testUndefinedRule() {
        $rules = new Rules($this->getLanguageMock());
        $rules->rules('nope');
    }

    /** @expectedException \LogicException */
    public function testWrongFormat() {
        $rules = new Rules($this->getLanguageMock());
        $rules->addMany([
            'token' => "string",
        ]);
    }

    /**
     * @return Language
     */
    private function getLanguageMock() {
        return $this->getMockBuilder(GreedyLanguage::class)->disableOriginalConstructor()->getMock();
    }
}
