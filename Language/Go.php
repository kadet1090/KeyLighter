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

use Kadet\Highlighter\Matcher\CommentMatcher;
use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Matcher\WordMatcher;
use Kadet\Highlighter\Parser\Rule;

class Go extends GreedyLanguage
{

    /**
     * Tokenization rules setup
     */
    public function setupRules()
    {
        $identifier = '[\p{L}\p{Nl}$_][\p{L}\p{Nl}$\p{Mn}\p{Mc}\p{Nd}\p{Pc}]*';
        $this->rules->addMany([
            'comment'          => new Rule(new CommentMatcher(['//'], [['/*', '*/']])),
            'keyword'          => new Rule(new WordMatcher([
                'break', 'default', 'func', 'interface', 'select', 'case', 'defer', 'go', 'map', 'struct', 'chan',
                'else', 'goto', 'package', 'switch', 'const', 'fallthrough', 'if', 'range', 'type', 'continue', 'for',
                'import', 'return', 'var',
            ])),
            'number'           => [
                'integer' => new Rule(new RegexMatcher('/\b([1-9]\d*|0x\x+|[0-7]+)\b/si')),
                'float'   => new Rule(new RegexMatcher('/\v((?:\d+\.\d*(?P<exponent>e[+-]?\d+)?|\.\d+(?&exponent)?|\d+(?&exponent))i?)\b/si')),
            ],
            'string'           => [
                'rune' => new Rule(new RegexMatcher('/(\'(?:\\\(?:[abfnrtv\\\'"]|[0-7]{3}|x\x{2})|u\x{4}|U\x{8})\')/si')),
                CommonFeatures::strings(['single' => '`', 'double' => '"']),
            ],

            'constant.special' => new Rule(new WordMatcher(['true', 'false', 'iota'])),
            'type'             => new Rule(new RegexMatcher('/((?:\*\s*)?(?:u?int(?:8|16|32|64|ptr)?|float(?:32|64)|complex(?:64|128)|byte|rune|string|error))/')),
            'symbol'           => [
                'function'  => new Rule(new RegexMatcher("/func\\s*($identifier)/")),
                'struct'    => new Rule(new RegexMatcher("/type\\s*($identifier)\\s*struct/")),
                'interface' => new Rule(new RegexMatcher("/type\\s*($identifier)\\s*interface/")),
            ],
            'call'             => new Rule(new RegexMatcher("/($identifier)\\s*\\(/i"), ['priority' => -1]),

            'operator' => [
                new Rule(new RegexMatcher('~((?>&{2}|<-|[+-]{2}|\|\||[+&=!/:%*^-|]?=|<{1,2}=?|>{1,2}=?|&^=?))~si')),
                'punctuation' => new Rule(new RegexMatcher('/([;,]|\.\.\.)/')),
            ],
        ]);
    }

    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'go';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['go', 'golang'],
            'mime'      => ['text/x-go', 'application/x-go', 'text/x-golang', 'application/x-golang'],
            'extension' => ['*.go']
        ];
    }
}
