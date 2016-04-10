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
use Kadet\Highlighter\Parser\OpenRule;
use Kadet\Highlighter\Parser\Rule;

class CSharp extends C
{
    public function setupRules()
    {
        parent::setupRules();

        $this->rules->remove('preprocessor');
        $this->rules->remove('call.preprocessor');

        $this->rules->add('preprocessor', new OpenRule(new RegexMatcher('/^\s*(#)/m')));
        $this->rules->add('call.preprocessor', new Rule(new RegexMatcher('/^\s*#(\w+)/m'), [
            'context' => ['preprocessor']
        ]));

        $this->rules->remove('keyword');
        $this->rules->remove('symbol.type', 0);

        $this->rules->add('keyword', new Rule(new WordMatcher([
            'abstract', 'as', 'base', 'bool', 'break', 'case', 'catch', 'char', 'checked', 'class', 'const', 'continue',
            'default', 'delegate', 'do', 'else', 'enum', 'event', 'explicit', 'extern', 'finally', 'fixed', 'for',
            'foreach', 'goto', 'if', 'implicit', 'in', 'interface', 'internal', 'is', 'lock', 'namespace', 'new',
            'object', 'operator', 'out', 'override', 'partial', 'params', 'private', 'protected', 'public', 'readonly', 'ref',
            'return', 'sealed', 'short', 'sizeof', 'stackalloc', 'static', 'string', 'struct', 'switch', 'throw', 'try',
            'typeof', 'unchecked', 'unsafe', 'using', 'virtual', 'volatile', 'var', 'while', 'yield',
            '__makeref', '__reftype', '__refvalue', '__arglist'
        ])));

        $this->rules->add('symbol.class', new Rule(new RegexMatcher('/(\w+)(?:\s+|\s*[*&]\s*)\w+\s*[={}();,]/')));
        $this->rules->add('symbol.class.template', new Rule(new RegexMatcher('/(\w+)\s*<.*?>/')));

        $this->rules->add('variable.special', new Rule(new RegexMatcher('/\b(this)\b/')));
        $this->rules->add('constant.special', new Rule(new WordMatcher(['true', 'false', 'null'])));
    }

    public function getIdentifier()
    {
        return 'csharp';
    }
}
