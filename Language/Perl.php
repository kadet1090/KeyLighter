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
use Kadet\Highlighter\Parser\OpenRule;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\ContextualToken;
use Kadet\Highlighter\Parser\Token\LanguageToken;
use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\Highlighter\Parser\Validator\Validator;

class Perl extends GreedyLanguage
{
    
    /**
     * Tokenization rules definition
     */
    public function setupRules()
    {
        $identifier = '\w+';
        $number = '[+-]?(?=\d|\.\d)\d*(\.\d*)?([Ee]([+-]?\d+))?';

        $this->rules->addMany([
            'string'  => CommonFeatures::strings(['single' => '\'', 'double' => '"'], [
                'context' => ['!keyword', '!comment', '!string', '!language', '!number'],
            ]),

            'comment' => new Rule(new CommentMatcher(['#'])),

            'keyword' => new Rule(new WordMatcher([
                'case', 'continue', 'do', 'else', 'elsif', 'for', 'foreach',
                'if', 'last', 'my', 'next', 'our', 'redo', 'reset', 'then',
                'unless', 'until', 'while', 'use', 'print', 'new', 'BEGIN',
                'sub', 'CHECK', 'INIT', 'END', 'return', 'exit'
            ])),

            'keyword.escape' => new Rule(new RegexMatcher('/(\\\.)/'), [
                'context' => ['string']
            ]),

            'string.nowdoc'  => new Rule(
                new RegexMatcher('/<<\s*\'(\w+)\';(?P<string>.*?)\n\1/sm', [
                    'string' => Token::NAME,
                          0  => 'keyword.nowdoc'
                ]), ['context' => ['!comment']]
            ),

            'language.shell' => new Rule(new SubStringMatcher('`'), [
                'context' => ['!keyword.escape', '!comment', '!string', '!keyword.nowdoc'],
                'factory' => new TokenFactory(ContextualToken::class),
            ]),

            'variable.scalar' => new Rule(new RegexMatcher("/(\\\$$identifier)/")),
            'variable.array'  => new Rule(new RegexMatcher("/(\\@$identifier)/")),
            'variable.hash'   => new Rule(new RegexMatcher("/(\\%$identifier)/")),

            'variable.property'   => new Rule(new RegexMatcher("/\\\$$identifier{($identifier)}/")),

            // Stupidly named var? Perl one, for sure.
            'variable.special'   => new Rule(new RegexMatcher('/([$@%][^\s\w]+[\w]*)/')),

            'operator' => [
                new Rule(new RegexMatcher('/(-[rwxoRWXOezsfdlpSbctugkTBMAC])/')),
                new Rule(new WordMatcher([
                    'not', 'and', 'or', 'xor', 'goto', 'last', 'next', 'redo', 'dump',
                    'eq', 'ne', 'cmp', 'not', 'and', 'or', 'xor'
                ], ['atomic' => true])),
            ],

            'call' => new Rule(new RegexMatcher('/([a-z]\w+)(?:\s*\(|\s+[$%@"\'`{])/i')),

            'number' => [
                new Rule(new RegexMatcher("/(\\b|\"|')$number\\1/", [
                    0 => Token::NAME
                ]), ['priority' => 5]),
            ],

            'string.regex' => [
                new OpenRule(new RegexMatcher('#~\s*[ms]?(/).*?/#m'), [
                    'context' => Validator::everywhere()
                ]),
                new OpenRule(new RegexMatcher('#~\s*(s/).*?/#m')),

                new Rule(new RegexMatcher('#(?=\/.*?(/[gimuy]{0,5}))#m'), [
                    'priority' => 1,
                    'factory'  => new TokenFactory(ContextualToken::class),
                    'context'  => ['!keyword.escape', 'string.regex']
                ])
            ],
            
            'symbol.iterator' => [
                new Rule(new RegexMatcher('#(<\w+>)#s'))
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
        return 'perl';
    }

    public function getEnds($embedded = false)
    {
        return [
            parent::getEnds($embedded),
            new CloseRule(new SubStringMatcher('__END__'), [
                'factory'  => new TokenFactory(LanguageToken::class),
                'language' => $this
            ])
        ];
    }
}
