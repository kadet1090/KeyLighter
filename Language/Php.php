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
use Kadet\Highlighter\Parser\CloseRule;
use Kadet\Highlighter\Parser\Token\LanguageToken;
use Kadet\Highlighter\Parser\Token\ContextualToken;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\OpenRule;
use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\TokenFactory;

class Php extends Language
{
    /**
     * Tokenization rules
     */
    public function setupRules()
    {
        $this->rules->addMany([
            'string.single' => new Rule(new SubStringMatcher('\''), [
                'context' => ['!keyword.escape', '!comment', '!string', '!keyword.nowdoc'],
                'factory' => new TokenFactory(ContextualToken::class),
            ]),

            'string.double' => new Rule(new SubStringMatcher('"'), [
                'context' => ['!keyword.escape', '!comment', '!string'],
                'factory' => new TokenFactory(ContextualToken::class),
            ]),

            'string.heredoc' => new Rule(new RegexMatcher('/<<<\s*(\w+)(?P<string>.*?)\n\1;/sm', ['string' => Token::NAME, 0 => 'keyword.heredoc']), ['context' => ['!comment']]),
            'string.nowdoc'  => new Rule(new RegexMatcher('/<<<\s*\'(\w+)\'(?P<string>.*?)\n\1;/sm', ['string' => Token::NAME, 0 => 'keyword.nowdoc']), ['context' => ['!comment']]),

            'variable' => new Rule(new RegexMatcher('/[^\\\](\$[a-z_]\w*)/i'), [
                'context' => ['*comment.docblock', '!string.nowdoc', '!string.single', '!comment']
            ]),
            'variable.property' => new Rule(new RegexMatcher('/(?=(?:\w|\)|\])\s*->([a-z_]\w*))/i'), [
                'priority' => -2
            ]),

            'symbol.function' => new Rule(new RegexMatcher('/function\s+([a-z_]\w+)\s*\(/i')),
            'symbol.class'    => [
                new Rule(new RegexMatcher('/(?:class|new|use|extends)\s+([\w\\\]+)/i')),
                new Rule(new RegexMatcher('/([\w\\\]+)::/i')),
                new Rule(new RegexMatcher('/@(?:var|property(?:-read|-write)?)\s+([^\$][\w\\\]+)/i'), ['context' => ['comment.docblock']]),
            ],

            'symbol.class.interface' => [
                new Rule(new RegexMatcher('/interface\s+([\w\\\]+)/i')),
                new Rule(new RegexMatcher('/implements\s+([\w\\\]+)(?:,\s*([\w\\\]+))*/i'), [
                    1 => Token::NAME,
                    2 => Token::NAME
                ]),
            ],

            'symbol.namespace' => new Rule(new RegexMatcher('/(\\\{0,2}(?:\w+\\\{1,2})+\w+)/i'), [
                'context' => ['*symbol', '*none']
            ]),

            'keyword.escape' => new Rule(new RegexMatcher('/(\\\(?:x[0-9a-fA-F]{1,2}|u\{[0-9a-fA-F]{1,6}\}|[0-7]{1,3}|.))/i'), [
                'context' => ['string']
            ]),

            'comment' => new Rule(new CommentMatcher(['//', '#'], [
                '$.docblock' => ['/**', '*/'],
                ['/* ', '*/']
            ])),

            'keyword.annotation' => new Rule(new RegexMatcher('/[\s]+(@[\w-]+)/i'), [
                'context' => ['comment.docblock']
            ]),

            'call' => new Rule(new RegexMatcher('/([a-z_]\w*)\s*\(/i'), ['priority' => -1]),

            'constant' => new Rule(new WordMatcher(array_merge([
                '__CLASS__', '__DIR__', '__FILE__', '__FUNCTION__',
                '__LINE__', '__METHOD__', '__NAMESPACE__', '__TRAIT__',
            ], array_keys(get_defined_constants(true)["Core"]))), ['priority' => -2]),
            'constant.static' => new Rule(new RegexMatcher('/(?:[\w\\\]+::|const\s+)(\w+)/i'), ['priority' => -2]),

            'keyword' => new Rule(new WordMatcher([
                '__halt_compiler', 'abstract', 'and', 'array',
                'as', 'break', 'callable', 'case', 'catch',
                'class', 'clone', 'const', 'continue', 'declare',
                'default', 'die', 'do', 'echo', 'else', 'elseif',
                'empty', 'enddeclare', 'endfor', 'endforeach', 'endif',
                'endswitch', 'endwhile', 'eval', 'exit', 'extends',
                'final', 'finally', 'for', 'foreach', 'function',
                'global', 'goto', 'if', 'implements', 'include', 'include_once',
                'instanceof', 'insteadof', 'interface', 'isset', 'list',
                'namespace', 'new', 'or', 'print', 'private', 'protected',
                'public', 'require', 'require_once', 'return', 'static',
                'switch', 'throw', 'trait', 'try', 'unset', 'parent', 'self',
                'use', 'var', 'while', 'xor', 'yield'
            ]), ['context' => ['!string', '!variable', '!comment']]),

            'keyword.cast' => new Rule(
                new RegexMatcher('/(\((?:int|integer|bool|boolean|float|double|real|string|array|object|unset)\))/')
            ),

            'delimiter' => new Rule(new RegexMatcher('/(<\?php|<\?=|\?>)/')),
            'number'    => new Rule(new RegexMatcher('/(-?(?:0[0-7]+|0[xX][0-9a-fA-F]+|0b[01]+|\d+))/')),

            'operator.punctuation' => new Rule(new WordMatcher([',', ';'], ['separated' => false]), ['priority' => 0]),
        ]);
    }

    /** {@inheritdoc} */
    public function getEnds($embedded = false)
    {
        return $embedded ? [
            new OpenRule(new RegexMatcher('/(<\?php|<\?=)/si'), [
                'factory'  => new TokenFactory(LanguageToken::class),
                'priority' => 1000,
                'context'  => ['*'],
                'inject'   => $this,
                'language' => null
            ]),
            new CloseRule(new RegexMatcher('/(\?>|$)/'), [
                'context'  => ['!string', '!comment'],
                'priority' => 1000,
                'factory'  => new TokenFactory(LanguageToken::class),
                'language' => $this
            ])
        ] : parent::getEnds(false);
    }

    public function getIdentifier()
    {
        return 'php';
    }
}
