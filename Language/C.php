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
use Kadet\Highlighter\Parser\Token\LanguageToken;
use Kadet\Highlighter\Parser\Token\TerminatorToken;
use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\Highlighter\Parser\Validator\Validator;

class C extends GreedyLanguage
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

            'meta.newline' => new CloseRule(new RegexMatcher('/()\R/m'), [
                'factory' => new TokenFactory(TerminatorToken::class),
                'context' => ['!operator.escape', '*string', '*call.preprocessor'],
                'closes'  => ['string.double', 'preprocessor'],
            ]),

            'preprocessor' => new OpenRule(new RegexMatcher('/^(#)/m'), [
                'context' => Validator::everywhere(),
                'priority' => -1
            ]),

            'call' => [
                'preprocessor' => new Rule(new RegexMatcher('/^#\s*(\w+)\b/m'), [
                    'context' => ['preprocessor']
                ]),
                new Rule(new RegexMatcher('/([a-z_]\w*)\s*\(/i'), ['priority' => -1]),
            ],

            'operator.escape' => new Rule(new RegexMatcher('/(\\\(?:\R|.|[0-7]{3}|x\x{2}))/s'), [
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
                'context' => ['!operator.escape', '!comment', '!string'],
            ])),

            'symbol.type' => [
                new Rule(new RegexMatcher('/(\w+)(?:\s+|\s*\*\s*)\w+\s*[=();,]/'), ['name' => 'universal']),
                new Rule(new WordMatcher(['int', 'float', 'double', 'char', 'void', 'long', 'short', 'signed', 'unsigned']), ['name' => 'builtin'])
            ],

            'comment' => [
                new Rule(new CommentMatcher(['//'], [['/*', '*/']]), ['priority' => 2])
            ],

            'variable.property' => new Rule(new RegexMatcher('/(?=(?:\w|\)|\])\s*(?:->|\.)\s*([a-z_]\w*))/'), ['priority' => 0]),

            'number' => new Rule(new RegexMatcher('/\b(-?(?:0x[\da-f]+|\d*\.?\d+(?:e[+-]?\d+)?)[ful]*)\b/i')),
            'operator' => [
                'punctuation' => new Rule(new RegexMatcher('/([;,])/'), ['priority' => 0]),
                new Rule(new RegexMatcher('/([*&])/'), ['priority' => 0]),
                new Rule(new RegexMatcher('/([!+\-\/*&|^<>=]{1,2}=?)/'), ['priority' => 0])
            ],

            'language.c' => [
                new Rule(new RegexMatcher('/^#define\s+\w+(.*?)(?>[^\\\]\r\n|[^\\\\\r]\n|\Z)/sim'), [
                    'factory' => new TokenFactory(LanguageToken::class),
                    'inject'  => $this,
                    'context' => ['preprocessor'],
                    'priority' => 10
                ]),
            ]
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

    public static function getMetadata()
    {
        return [
            'name'      => ['c'],
            'mime'      => ['text/x-csrc', 'text/x-chdr'],
            'extension' => ['*.c', '*.h', '*.idc']
        ];
    }
}
