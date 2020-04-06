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


use Kadet\Highlighter\Parser\Context;
use Kadet\Highlighter\Parser\Validator\DelegateValidator;
use Kadet\Highlighter\Parser\Validator\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function testInValidation() {
        $validator = new Validator(['test']);
        
        $this->assertTrue($validator->validate(Context::fromArray(['test'])));
        $this->assertTrue($validator->validate(Context::fromArray(['test', 'smth'])));
        $this->assertFalse($validator->validate(Context::fromArray([])));
        $this->assertFalse($validator->validate(Context::fromArray(['smth'])));
    }

    public function testNotInValidation() {
        $validator = new Validator(['!test']);

        $this->assertFalse($validator->validate(Context::fromArray(['test'])));
        $this->assertFalse($validator->validate(Context::fromArray(['test', 'smth'])));
        $this->assertTrue($validator->validate(Context::fromArray([])));
        $this->assertTrue($validator->validate(Context::fromArray(['smth'])));
    }


    public function testInOneOfContextValidation()
    {
        $validator = new Validator(['*first', '*second']);

        $this->assertTrue($validator->validate(Context::fromArray(['first'])));
        $this->assertTrue($validator->validate(Context::fromArray(['second'])));
        $this->assertFalse($validator->validate(Context::fromArray([])));
        $this->assertFalse($validator->validate(Context::fromArray(['other'])));
    }

    public function testSubTokens()
    {
        $validator = new Validator(['token.test', '!token']);

        $this->assertTrue($validator->validate(Context::fromArray(['token.test'])));
        $this->assertTrue($validator->validate(Context::fromArray(['token.test.smth'])));
        $this->assertFalse($validator->validate(Context::fromArray(['token'])));
    }

    public function testRegex()
    {
        $validator = new Validator(['+~token\.[ab]']);

        $this->assertTrue($validator->validate(Context::fromArray(['token.a'])));
        $this->assertTrue($validator->validate(Context::fromArray(['token.b'])));
        $this->assertFalse($validator->validate(Context::fromArray(['token'])));
    }

    public function testNotInRegex()
    {
        $validator = new Validator(['!~token\.[ab]']);

        $this->assertFalse($validator->validate(Context::fromArray(['token.a'])));
        $this->assertFalse($validator->validate(Context::fromArray(['token.b'])));
        $this->assertTrue($validator->validate(Context::fromArray(['token'])));
    }

    public function testExactly()
    {
        $validator = new Validator(['+@token']);

        $this->assertFalse($validator->validate(Context::fromArray(['token.a'])));
        $this->assertFalse($validator->validate(Context::fromArray(['token.b'])));
        $this->assertTrue($validator->validate(Context::fromArray(['token'])));
    }

    public function testInAll()
    {
        $rule = Validator::everywhere();

        $this->assertTrue($rule->validate(Context::fromArray(['first'])));
        $this->assertTrue($rule->validate(Context::fromArray(['second'])));
        $this->assertTrue($rule->validate(Context::fromArray([])));
        $this->assertTrue($rule->validate(Context::fromArray(['other'])));
    }

    public function testInNone()
    {
        $rule = new Validator();

        $this->assertFalse($rule->validate(Context::fromArray(['first'])));
        $this->assertFalse($rule->validate(Context::fromArray(['second'])));
        $this->assertTrue($rule->validate(Context::fromArray([])));
        $this->assertFalse($rule->validate(Context::fromArray(['other'])));
    }

    public function testCallableValidator()
    {
        $validator = new DelegateValidator(function ($context) {
            return in_array('bar', $context->stack) && !in_array('foo', $context->stack);
        });

        $this->assertFalse($validator->validate(Context::fromArray(['test'])));
        $this->assertTrue($validator->validate(Context::fromArray(['bar'])));
        $this->assertTrue($validator->validate(Context::fromArray(['bar', 'smth'])));
        $this->assertFalse($validator->validate(Context::fromArray(['bar', 'foo'])));
    }
}
