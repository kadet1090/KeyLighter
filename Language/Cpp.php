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

namespace Kadet\Highlighter\Language;


use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Matcher\WordMatcher;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\Token;

class Cpp extends C
{
    public function setupRules()
    {
        parent::setupRules();

        $this->rules->remove('keyword');
        $this->rules->remove('symbol.type', 0);

        $this->rules->add('keyword', new Rule(
            new WordMatcher([
                'auto', 'align(?:as|of)', 'and(?:_eq)?', 'asm', 'auto', 'bit(and|or)', 'break', 'case', 'catch', 'class',
                'compl', 'concept', 'const(?:_cast|expr)?', 'continue', 'decltype', 'default', 'delete', 'do', 'double',
                'dynamic_cast', 'else', 'enum', 'explicit', 'export', 'extern', 'for', 'friend', 'goto', 'if', 'inline',
                'mutable', 'namespace', 'new', 'noexcept', 'not(?:_eq|ptr)', 'operator', 'or(?:_eq)?', 'private',
                'protected', 'public', 'register', 'reinterpret_cast', 'requires', 'return', 'sizeof',
                'static(?:_assert|_cast)?', 'struct', 'switch', 'template', 'thread_local', 'throw', 'try', 'typedef',
                'typeid', 'typename', 'union', 'using', 'virtual', 'volatile', 'while', 'xor(?:_eq)?',
            ], ['escape' => false]), [
                'priority' => 2
            ]
        ));

        $this->rules->add('symbol.type', new Rule(new WordMatcher(['bool', 'wchar'])));
        $this->rules->add('constant.special', new Rule(new WordMatcher(['false', 'null', 'true'])));
        $this->rules->add('symbol.class', new Rule(new RegexMatcher('/(\w+)(?:\s+|\s*[*&]\s*)\w+\s*[={}();,]/')));
        $this->rules->add('symbol.class.template', new Rule(new RegexMatcher('/(\w+)\s*<.*?>/')));

        $this->rules->add('symbol.namespace', new Rule(new RegexMatcher('/((?::)?(\w+::)+)(\w+)/', [
            1 => Token::NAME,
            2 => 'symbol.class'
        ])));
    }

    public function getIdentifier()
    {
        return 'cpp';
    }
}
