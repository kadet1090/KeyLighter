<?php
/**
 * Highlighter
 *
 * Copyright (C) 2015, Some right reserved.
 * @author Kacper "Kadet" Donat <kadet1090@gmail.com>
 *
 * Contact with author:
 * Xmpp: kadet@jid.pl
 * E-mail: kadet1090@gmail.com
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter\Language;


use Kadet\Highlighter\Matcher\CommentMatcher;
use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Matcher\SubStringMatcher;
use Kadet\Highlighter\Matcher\WordMatcher;
use Kadet\Highlighter\Parser\OpenRule;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\TokenFactory;

/**
 * Class JavaScriptLanguage
 *
 * @package Kadet\Highlighter\Language
 *
 * @property bool $variables
 */
class JavaScriptLanguage extends Language
{
    protected $_options = [
        'variables' => true,
        'methods'   => true
    ];

    const IDENTIFIER = '[\p{L}\p{Nl}$_][\p{L}\p{Nl}$\p{Mn}\p{Mc}\p{Nd}\p{Pc}]*';

    public function getRules()
    {
        $rules = [
            'string.single' => new Rule(new SubStringMatcher('\''), [
                'context' => ['!keyword.escape', '!comment', '!string', '!keyword.nowdoc'],
                'factory' => new TokenFactory('Kadet\\Highlighter\\Parser\\MarkerToken'),
            ]),

            'string.double' => new Rule(new SubStringMatcher('"'), [
                'context' => ['!keyword.escape', '!comment', '!string'],
                'factory' => new TokenFactory('Kadet\\Highlighter\\Parser\\MarkerToken'),
            ]),
        ];

        if($this->variables) {
            $rules = array_merge($rules, [
                'variable' => new Rule(new RegexMatcher('/(' . self::IDENTIFIER . ')/iu'), ['priority' => -10000]),
                'variable.property' => new Rule(new RegexMatcher('/(?=(?:\w|\)|\])\s*\.([a-z_]\w*))/i'), [
                    'priority' => -2
                ]),
            ]);
        }

        $rules = array_merge($rules, [
            'symbol.function' => new Rule(new RegexMatcher('/function\s+([a-z_]\w+)\s*\(/i')),

            'keyword.escape' => new Rule(new RegexMatcher('/(\\\(?:x[0-9a-fA-F]{1,2}|u\{[0-9a-fA-F]{1,6}\}|[0-7]{1,3}|.))/i'), [
                'context' => ['string']
            ]),

            'comment' => new Rule(new CommentMatcher(['//'], [
                ['/*', '*/']
            ])),

            'call' => new Rule(new RegexMatcher('/(' . self::IDENTIFIER . ')\s*\(/iu'), ['priority' => -1]),

            'keyword' => new Rule(new WordMatcher([
                'do', 'if', 'in', 'for', 'let', 'new', 'try', 'var', 'case', 'else', 'enum', 'eval', 'false',
                'null', 'this', 'true', 'void', 'with', 'break', 'catch', 'class', 'const', 'super', 'throw',
                'while', 'yield', 'delete', 'export', 'import', 'public', 'return', 'static', 'switch',
                'typeof', 'default', 'extends', 'finally', 'package', 'private', 'continue', 'debugger',
                'function', 'arguments', 'interface', 'protected', 'implements', 'instanceof',
            ]), ['context' => ['!string', '!variable', '!comment']]),

            'number' => new Rule(new RegexMatcher('/(-?(?:0[0-7]+|0[xX][0-9a-fA-F]+|0b[01]+|\d+))/')),

            'operator.punctuation' => new Rule(new WordMatcher([',', ';'], ['separated' => false]), ['priority' => 0]),
            'operator' => new Rule(new WordMatcher([
                '->', '++', '--', '-', '+', '/', '*', '**', '||', '&&', '^', '%', '&', '@', '!', '|', ':', '.'
            ], ['separated' => false]), ['priority' => 0]),

            'string.regex' => [
                new OpenRule(new RegexMatcher('#(?>[\[=(?:+,!]|^|return|=>|&&|\|\|)\s*(/).*?/#m')),
                new Rule(new RegexMatcher('#\/.*(/[gimuy]{0,5})#m'), [
                    'priority' => 1,
                    'factory' => new TokenFactory('Kadet\Highlighter\Parser\MarkerToken'),
                    'context' => ['!keyword.escape', 'string.regex']
                ])
            ]
        ]);

        return $rules;
    }

    public function getIdentifier()
    {
        return 'javascript';
    }
}