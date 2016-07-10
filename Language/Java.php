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

        $this->rules->rule('symbol.annotation')->setMatcher(new RegexMatcher('/(@[\w\.]+)\s*(?:(?P<arguments>\((?>[^()]+|(?&arguments))*\))?)/si', [
            1 => Token::NAME
        ]));
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
