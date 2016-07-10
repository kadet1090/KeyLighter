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
use Kadet\Highlighter\Parser\Token\ContextualToken;
use Kadet\Highlighter\Parser\Token\LanguageToken;
use Kadet\Highlighter\Parser\TokenFactory;

class Ruby extends GreedyLanguage
{

    /**
     * Tokenization rules setup
     */
    public function setupRules()
    {
        $function = '[a-z]\w*[!?]?';
        $this->rules->addMany([
            'comment' => [
                new Rule(new CommentMatcher(['#']), ['context' => ['!string.regex', '!string']]),
                'doc' => [
                    new OpenRule(new RegexMatcher('/^(=begin)/m')),
                    new CloseRule(new RegexMatcher('/^(=end)/m')),
                ]
            ],
            'keyword' => new Rule(new WordMatcher([
                'BEGIN', 'class', 'ensure', 'when', 'END', 'def', 'not', 'super', 'while',
                'alias', 'defined', 'for', 'or', 'then', 'yield', 'and', 'do', 'if', 'redo', 'begin', 'else',
                'in', 'rescue', 'undef', 'break', 'elsif', 'module', 'retry', 'unless', 'case', 'end', 'next', 'return',
                'until',
            ], ['case-sensitivity' => true])),
            'string' => [
                CommonFeatures::strings(
                    ['single' => '\'', 'double' => '"'],
                    ['context' => ['!operator.escape', '!comment', '!string', '!expression']]
                ),
                'generalized' => [
                    // TODO: Generalized strings
                ]
            ],
            'operator.escape' => new Rule(new RegexMatcher('/(\\\.)/'), [
                'context' => ['string']
            ]),
            'constant' => [
                new Rule(new RegexMatcher('/(?:[a-z_])?::([a-z_]\w*)/i')),
                'special' => new Rule(new WordMatcher(['self', 'nil', 'true', 'false', '__FILE__', '__LINE__']))
            ],
            'variable' => [
                'global'   => new Rule(new RegexMatcher('/(?:[^\\\]|^)(\$\w*)/i')),
                'property' => new Rule(new RegexMatcher('/(?:[^\\\]|^)(@{1,2}\w*)/i')),
            ],
            // JS + Perl = Ruby?
            'string.regex' => [
                new OpenRule(new RegexMatcher('#(?>[\[=(?:+,!~]|^|return|=>|&&|\|\|)\s*(/).*?/#sm'), [
                    'context' => ['!comment']
                ]),
                new Rule(new RegexMatcher('#(?=\/.*?(/[mixounse]*))#sm'), [
                    'priority' => 2,
                    'factory'  => new TokenFactory(ContextualToken::class),
                    'context'  => ['!operator.escape', 'string.regex']
                ])
            ],
            'call' => [
                new Rule(new RegexMatcher("/($function)\\s*\\(/i"), ['priority' => 2]),
                new Rule(new RegexMatcher(
                    // fixme: expression handling smth[blah].func
                    "/(?<![^\\\\]\\\\)(?<=\\n|\\{|\\(|\\}|\\|\\||or|&&|and|=|;)\\s*(?:\\w+(?:::|\\.))?($function)(?:[\\h\r]*\$|\\h+['\":\\w])/im"
                ), ['priority' => 0]),
            ],
            'symbol' => [
                'symbol'   => new Rule(new RegexMatcher('/[^\B:](:[a-z_]\w*)/i')),
                'class'    => new Rule(new RegexMatcher('/class\s+([a-z_]\w*)/i')),
                'function' => new Rule(new RegexMatcher('/def\s+(?:\[\]\s*|\*\s*|\w+\.){0,2}([a-z_]\w*)/i')),
            ],
            'expression.in-string' => new Rule(new RegexMatcher('/(?=(\#\{((?>[^\#{}]+|(?1))+)\}))/x'), [
                'context' => ['string.double'],
                'factory' => new TokenFactory(LanguageToken::class),
                'inject'  => $this
            ]),

            'number'    => new Rule(new RegexMatcher('/((?:-|\b)(?:0[0-7]+|0[xX][0-9a-fA-F]+|0b[01]+|\d+))/')),
        ]);
    }

    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'ruby';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['ruby'],
            'mime'      => ['text/x-ruby', 'application/x-ruby'],
            'extension' => ['*.rb', '*.rbw', 'Rakefile', '*.rake', '*.gemspec', '*.rbx', '*.duby', 'Gemfile'],
        ];
    }
}
