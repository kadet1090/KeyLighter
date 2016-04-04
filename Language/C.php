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
use Kadet\Highlighter\Parser\CloseRule;
use Kadet\Highlighter\Parser\OpenRule;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\TerminatorToken;
use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\Highlighter\Parser\Validator\Validator;

class C extends Language
{
    
    /**
     * Tokenization rules setup
     */
    public function setupRules()
    {
        $this->rules->addMany([
            'keyword' => new Rule(new WordMatcher([
                'auto', 'break', 'case', 'const', 'continue', 'default', 'do', 'else', 'enum', 'extern', 'for', 'goto',
                'if', 'register', 'return', 'sizeof', 'static', 'struct', 'switch', 'typedef', 'union', 'volatile',
                'while',
            ])),

            'meta.newline' => new CloseRule(new RegexMatcher('/()\r?\n/'), [
                'factory' => new TokenFactory(TerminatorToken::class),
                'context' => ['!keyword.escape', '*string', '*call.preprocessor'],
                'closes'  => ['string.double', 'preprocessor']
            ]),

            'preprocessor' => new OpenRule(new RegexMatcher('/^(#)/m'), [
                'context' => Validator::everywhere()
            ]),

            'call' => [
                'preprocessor' => new Rule(new RegexMatcher('/^#\s*(\w+)\b/m'), [
                    'context' => ['preprocessor']
                ]),
                new Rule(new RegexMatcher('/([a-z_]\w*)\s*\(/i'), ['priority' => -1]),
            ],

            'keyword.escape' => new Rule(new RegexMatcher('/(\\\(?:.|[0-7]{3}|x\x{2}))/'), [
                'context' => Validator::everywhere()
            ]),

            'keyword.format' => new Rule(
                new RegexMatcher('/(%[diuoxXfFeEgGaAcspn%][-+#0]?(?:[0-9]+|\*)?(?:\.(?:[0-9]+|\*))?)/'), [
                    'context' => ['string']
                ]
            ),

            'string' => array_merge([
                new Rule(new RegexMatcher('/(<.*?>)/'), [
                    'context' => ['preprocessor']
                ])
            ], CommonFeatures::strings(['single' => '\'', 'double' => '"'], [
                'context' => ['!keyword.escape', '!comment', '!string'],
            ])),

            'symbol.type' => [
                new Rule(new RegexMatcher('/(\w+)(?:\s+|\s*\*\s*)\w+\s*[=();,]/')),
                new Rule(new WordMatcher(['int', 'float', 'double', 'char', 'void', 'long', 'short', 'signed', 'unsigned']))
            ],

            'comment' => [
                new Rule(new CommentMatcher(['//'], [['/*', '*/']]), ['priority' => 2])
            ],

            'number' => new Rule(new RegexMatcher('/\b(-?(?:0x[\da-f]+|\d*\.?\d+(?:e[+-]?\d+)?)[ful]*)\b/i')),
            'operator' => new Rule(new RegexMatcher('/([*&])/'), [
                'priority' => 0
            ]),
        ]);
    }

    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'c';
    }
}
