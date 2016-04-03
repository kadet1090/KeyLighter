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
use Kadet\Highlighter\Matcher\SubStringMatcher;
use Kadet\Highlighter\Matcher\WordMatcher;
use Kadet\Highlighter\Parser\CloseRule;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\ContextualToken;
use Kadet\Highlighter\Parser\Token\TerminatorToken;
use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\Highlighter\Parser\Validator\Validator;

class Python extends Language
{

    /**
     * Tokenization rules setup
     */
    public function setupRules()
    {
        $standard = new Validator(['!string', '!comment']);

        $this->rules->addMany([
            'keyword' => new Rule(new WordMatcher([
                'and', 'del', 'from', 'not', 'while', 'as', 'elif', 'global', 'or', 'with', 'assert', 'else', 'if',
                'pass', 'yield', 'break', 'except', 'import', 'print', 'class', 'exec', 'in', 'raise', 'continue',
                'finally', 'is', 'return', 'def', 'for', 'lambda', 'try',
            ])),

            'operator' => new Rule(
                new RegexMatcher('/([-+%=]=?|!=|\*\*?=?|\/\/?=?|<[<=>]?|>[=>]?|[&|^~])|\b(or|and|not)\b/'), [
                    'priority' => -1
                ]
            ),

            'expression' => new Rule(new RegexMatcher('/\{(\S+)\}/'), [
                'context' => ['string']
            ]),

            'variable' => [
                new Rule(new RegexMatcher('/[^\w.]([a-z_]\w*)\.\w/i')),
                'property' => new Rule(new RegexMatcher('/(?=(?:\w|\)|\])\s*\.([a-z_]\w*))/i'), [
                    'priority' => -2,
                    'context' => ['*none', '*expression']
                ]),
            ],


            'symbol' => [
                new Rule(new RegexMatcher('/import\s+([a-z_][\w.]*)(?:\s*,\s*([a-z_][\w.]*))*/i', [
                    1 => Token::NAME,
                    2 => Token::NAME,
                ])),
                'library' => new Rule(new RegexMatcher('/from\s+([a-z_][\w.]*)\s+import/i', [
                    1 => Token::NAME,
                ]))
            ],

            'keyword.escape' => new Rule(new RegexMatcher('/(\\\(?:.|[0-7]{3}|x\x{2}))/'), [
                'context' => Validator::everywhere()
            ]),

            'comment' => new Rule(new CommentMatcher(['#'])),
            'constant.special' => [
                new Rule(new WordMatcher(['True', 'False', 'NotImplemented', 'Ellipsis'], [
                    'case-sensitivity' => true
                ])),
                new Rule(new RegexMatcher('/\b(__\w+__)\b/'))
            ],
            'call' => new Rule(new RegexMatcher('/([a-z_]\w*)\s*\(/i'), ['priority' => 2]),

            'meta.newline' => new CloseRule(new RegexMatcher('/()\r?\n/'), [
                'factory' => new TokenFactory(TerminatorToken::class),
                'context' => ['!keyword.escape', 'string.single-line'],
                'closes'  => ['string.single-line.double', 'string.single-line.single']
            ]),

            'number' => new Rule(
                new RegexMatcher('/(-?(?:0[bo])?(?:(?:\d|0x[\da-f])[\da-f]*\.?\d*|\.\d+)(?:e[+-]?\d+)?j?)\b/')
            ),

            'string' => [
                'single-line' => [
                    'double' => new Rule(new RegexMatcher('/(?:^|[^"])(")(?=[^"]|$)/'), [
                        'factory' => new TokenFactory(ContextualToken::class),
                        'context' => $standard,
                    ]),
                    'single' => new Rule(new RegexMatcher('/(?:^|[^\'])(\')(?=[^\']|$)/'), [
                        'factory' => new TokenFactory(ContextualToken::class),
                        'context' => $standard,
                    ]),
                ],
                'multi-line' => [
                    'double' => new Rule(new SubStringMatcher('"""'), [
                        'factory'  => new TokenFactory(ContextualToken::class),
                        'context'  => $standard,
                        'priority' => 2,
                    ]),
                    'single' => new Rule(new SubStringMatcher('\'\'\''), [
                        'factory'  => new TokenFactory(ContextualToken::class),
                        'context'  => $standard,
                        'priority' => 2,
                    ]),
                ]
            ],

            'symbol.function' => new Rule(new RegexMatcher('/def\s+([a-z_]\w+)\s*\(/i')),
            'symbol.class'    => new Rule(new RegexMatcher('/class\s+([a-z_]\w+)/i')),
        ]);
    }

    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'python';
    }
}
