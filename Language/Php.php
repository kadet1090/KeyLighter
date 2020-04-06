<?php
/**
 * Highlighter
 *
 * Copyright (C) 2016, Some right reserved.
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
use Kadet\Highlighter\Matcher\DelegateRegexMatcher;
use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Matcher\WordMatcher;
use Kadet\Highlighter\Parser\CloseRule;
use Kadet\Highlighter\Parser\OpenRule;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\LanguageToken;
use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\Highlighter\Parser\TokenFactoryInterface;

class Php extends GreedyLanguage
{
    
    /**
     * Tokenization rules
     */
    public function setupRules()
    {
        $this->rules->addMany([
            'string' => CommonFeatures::strings(['single' => '\'', 'double' => '"'], [
                'context' => ['!operator.escape', '!comment', '!string', '!expression'],
            ]),

            'string.heredoc' => new Rule(new RegexMatcher('/<<<\s*(\w+)\R(?P<string>.*?)\R\1;/sm', ['string' => Token::NAME, 0 => 'keyword.heredoc']), ['context' => ['!comment']]),
            'string.nowdoc'  => new Rule(new RegexMatcher('/<<<\s*\'(\w+)\'\R(?P<string>.*?)\R\1;/sm', ['string' => Token::NAME, 0 => 'keyword.nowdoc']), ['context' => ['!comment']]),

            'variable' => new Rule(new RegexMatcher('/(?:[^\\\]|^)(\$[a-z_]\w*)/i'), [
                'context' => ['*comment.docblock', '!string.nowdoc', '!string.single', '!comment']
            ]),
            'variable.property' => new Rule(new RegexMatcher('/(?=(?:\w|\)|\])\s*->([a-z_]\w*))/i'), [
                'priority' => -2,
                'context' => ['*comment.docblock', '!string.nowdoc', '!string.single', '!comment']
            ]),

            'symbol.function' => new Rule(new RegexMatcher('/function\s+([a-z_]\w+)\s*\(/i')),
            'symbol.class'    => [
                new Rule(new RegexMatcher('/(?:class|new|use|extends)\s+([\w\\\]+)/i')),
                new Rule(new RegexMatcher('/([\w\\\]+)::/i')),
                new Rule(new RegexMatcher('/@(?:var|property(?:-read|-write)?)(?:\s+|\s+\$\w+\s+)([^$][\w\\\]+)/i'), ['context' => ['comment.docblock']]),
            ],
            
            'expression.in-string' => new Rule(new RegexMatcher('/(?=(\{\$((?>[^${}]+|(?1))+)\}))/x'), [
                'context' => ['string'],
                'factory' => new TokenFactory(LanguageToken::class),
                'inject'  => $this
            ]),

            'symbol.class.interface' => [
                new Rule(new RegexMatcher('/interface\s+([\w\\\]+)/i')),
                new Rule(new DelegateRegexMatcher(
                    '/implements\s+((?:[\w\\\]+)(?:,\s*([\w\\\]+))+)/i',
                    function($match, TokenFactoryInterface $factory) {
                        foreach (preg_split('/,\s*/', $match[1][0], 0, PREG_SPLIT_OFFSET_CAPTURE) as $interface) {
                            yield $factory->create(Token::NAME, [
                                'pos' => $match[1][1] + $interface[1],
                                'length' => strlen($interface[0])]
                            );
                        }
                    }
                )),
            ],

            'symbol.namespace' => [
                /*new Rule(new RegexMatcher('/(\\\{0,2}(?:\w+\\\{1,2})+)\w+/i'), [
                    'context' => ['*symbol', '*none']
                ]),*/

                new Rule(new RegexMatcher('/namespace\s*(\\\{0,2}(?:\w+\\\{1,2})+\w+);/i'), [
                    'context' => ['*symbol', '*none']
                ]),
            ],

            'operator.escape' => [
                new Rule(new RegexMatcher('/(\\\(?:x[0-9a-fA-F]{1,2}|u\{[0-9a-fA-F]{1,6}\}|[0-7]{1,3}|[^\'\\\]))/i'), [
                    'context' => ['string.double', '!operator.escape']
                ]),
                new Rule(new RegexMatcher('/(\\\[\'\\\])/i'), [
                    'context' => ['string', '!operator.escape']
                ]),
            ],

            'comment' => new Rule(new CommentMatcher(['//', '#'], [
                '$.docblock' => ['/**', '*/'],
                ['/*', '*/']
            ]), ['priority' => 4]),

            'symbol.annotation' => new Rule(new RegexMatcher('/[\s]+(@[\w-]+)/i'), [
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

    public static function getMetadata()
    {
        return [
            'name'      => ['php'],
            'mime'      => ['text/x-php', 'application/x-php'],
            'extension' => ['*.php', '*.phtml', '*.inc', '*.php?'],
            'injectable' => true
        ];
    }
}
