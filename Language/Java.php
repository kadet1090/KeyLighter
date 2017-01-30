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

class Java extends CSharp // evil
{
    public function setupRules()
    {
        parent::setupRules();

        $this->rules->rule('keyword')->setMatcher(new WordMatcher([
            'abstract', 'continue', 'for', 'new', 'switch', 'assert', 'default', 'goto', 'package', 'synchronized',
            'do', 'if', 'private', 'this', 'break', 'double', 'implements', 'protected', 'throw', 'else', 'import',
            'public', 'throws', 'case', 'enum', 'instanceof', 'return', 'transient', 'catch', 'extends', 'try', 'final',
            'interface', 'static', 'class', 'finally', 'strictfp', 'volatile', 'const', 'native', 'super', 'while'
        ]));

        $this->rules->rule('symbol.type', 1)->priority = 3;
        $this->rules->rule('symbol.type', 1)->setMatcher(new WordMatcher([
            'boolean', 'byte', 'char', 'short', 'int', 'long', 'float', 'double', 'void'
        ]));

        $this->rules->rule('symbol.annotation')->setMatcher(new RegexMatcher('/(@[\w\.]+)\s*(?:(?P<arguments>\((?>[^()]+|(?&arguments))*\))?)/si', [
            1 => Token::NAME
        ]));

        $this->rules->add('symbol.class', new Rule(new RegexMatcher('/\W(?>(?:public|protected|private|static|final|transient|volatile)\s+)+\s*([a-z][\w\_]+)(?><.*?>)?(?>\[\d*\])?\s+[a-z][\w_$]+[;,=]/si'), [
            'priority' => 2,
        ]));
        $this->rules->add('symbol.namespace', new Rule(new RegexMatcher('/(?:import|package)\s+([a-z][\w\.]+)\s*/si')));
    }

    public function getIdentifier()
    {
        return 'java';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['java'],
            'mime'      => ['text/x-java'],
            'extension' => ['*.java']
        ];
    }
}
