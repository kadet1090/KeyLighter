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


use Kadet\Highlighter\Parser\Validator\DelegateValidator;
use Kadet\Highlighter\Parser\Validator\Validator;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testInValidation() {
        $validator = new Validator(['test']);
        
        $this->assertTrue($validator->validate(['test']));
        $this->assertTrue($validator->validate(['test', 'smth']));
        $this->assertFalse($validator->validate([]));
        $this->assertFalse($validator->validate(['smth']));
    }

    public function testNotInValidation() {
        $validator = new Validator(['!test']);

        $this->assertFalse($validator->validate(['test']));
        $this->assertFalse($validator->validate(['test', 'smth']));
        $this->assertTrue($validator->validate([]));
        $this->assertTrue($validator->validate(['smth']));
    }


    public function testInOneOfContextValidation()
    {
        $validator = new Validator(['*first', '*second']);

        $this->assertTrue($validator->validate(['first']));
        $this->assertTrue($validator->validate(['second']));
        $this->assertFalse($validator->validate([]));
        $this->assertFalse($validator->validate(['other']));
    }

    public function testSubTokens()
    {
        $validator = new Validator(['token.test', '!token']);

        $this->assertTrue($validator->validate(['token.test']));
        $this->assertTrue($validator->validate(['token.test.smth']));
        $this->assertFalse($validator->validate(['token']));
    }

    public function testRegex()
    {
        $validator = new Validator(['+~token\.[ab]']);

        $this->assertTrue($validator->validate(['token.a']));
        $this->assertTrue($validator->validate(['token.b']));
        $this->assertFalse($validator->validate(['token']));
    }

    public function testNotInRegex()
    {
        $validator = new Validator(['!~token\.[ab]']);

        $this->assertFalse($validator->validate(['token.a']));
        $this->assertFalse($validator->validate(['token.b']));
        $this->assertTrue($validator->validate(['token']));
    }

    public function testExactly()
    {
        $validator = new Validator(['+@token']);

        $this->assertFalse($validator->validate(['token.a']));
        $this->assertFalse($validator->validate(['token.b']));
        $this->assertTrue($validator->validate(['token']));
    }

    public function testInAll()
    {
        $rule = Validator::everywhere();

        $this->assertTrue($rule->validate(['first']));
        $this->assertTrue($rule->validate(['second']));
        $this->assertTrue($rule->validate([]));
        $this->assertTrue($rule->validate(['other']));
    }

    public function testInNone()
    {
        $rule = new Validator();

        $this->assertFalse($rule->validate(['first']));
        $this->assertFalse($rule->validate(['second']));
        $this->assertTrue($rule->validate([]));
        $this->assertFalse($rule->validate(['other']));
    }

    public function testCallableValidator()
    {
        $validator = new DelegateValidator(function ($context) {
            return in_array('bar', $context) && !in_array('foo', $context);
        });

        $this->assertFalse($validator->validate(['test']));
        $this->assertTrue($validator->validate(['bar']));
        $this->assertTrue($validator->validate(['bar', 'smth']));
        $this->assertFalse($validator->validate(['bar', 'foo']));
    }
}
