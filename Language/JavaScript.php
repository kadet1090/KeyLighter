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
use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Matcher\SubStringMatcher;
use Kadet\Highlighter\Matcher\WordMatcher;
use Kadet\Highlighter\Parser\OpenRule;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\ContextualToken;
use Kadet\Highlighter\Parser\Token\MetaToken;
use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\Highlighter\Parser\Validator\Validator;

/**
 * Class JavaScriptLanguage
 *
 * @package Kadet\Highlighter\Language
 *
 * @property bool $variables
 */
class JavaScript extends GreedyLanguage
{
    
    protected $_options = [
        'variables' => false,
    ];

    const IDENTIFIER = '[\p{L}\p{Nl}$_][\p{L}\p{Nl}$\p{Mn}\p{Mc}\p{Nd}\p{Pc}]*';

    /**
     * Tokenization rules
     */
    public function setupRules()
    {
        // we need to allow all the tokens in json
        $this->rules->validator = new Validator(['*none', '*meta.json', '!comment']);

        $this->rules->addMany([
            'string' => CommonFeatures::strings(['single' => '\'', 'double' => '"'], [
                'context' => ['!operator.escape', '!comment', '!string'],
            ]),

            'symbol.function' => new Rule(new RegexMatcher('/function\s+([a-z_]\w+)\s*\(/i')),

            'operator.escape' => new Rule(new RegexMatcher('/(\\\(?:x[0-9a-fA-F]{1,2}|u\{[0-9a-fA-F]{1,6}\}|[0-7]{1,3}|.))/i'), [
                'context' => ['string']
            ]),

            'comment' => new Rule(new CommentMatcher(['//'], [['/*', '*/']]), ['priority' => 3]),

            'call' => new Rule(new RegexMatcher('/(' . self::IDENTIFIER . ')\s*\(/iu'), ['priority' => -1]),

            'keyword' => new Rule(new WordMatcher([
                'do', 'if', 'in', 'for', 'let', 'new', 'try', 'var', 'case', 'else', 'enum', 'eval',
                'void', 'with', 'break', 'catch', 'class', 'const', 'super', 'throw',
                'while', 'yield', 'delete', 'export', 'import', 'public', 'return', 'static', 'switch',
                'typeof', 'default', 'extends', 'finally', 'package', 'private', 'continue', 'debugger',
                'function', 'arguments', 'interface', 'protected', 'implements', 'instanceof', 'get', 'set', 'from'
            ]), ['context' => ['!string', '!comment', '!symbol', '!call']]),

            'constant.special' => new Rule(new WordMatcher(['null', 'true', 'false'])),
            'variable.special' => new Rule(new SubStringMatcher('this')),

            'number' => new Rule(new RegexMatcher('/\b(-?(?:0[0-7]+|0[xX][0-9a-fA-F]+|0b[01]+|\d+))\b/')),

            'operator.punctuation' => new Rule(new WordMatcher([',', ';'], ['separated' => false]), ['priority' => 0]),
            'operator'             => new Rule(new RegexMatcher('/(=>|\+{1,2}|-{1,2}|={1,3}|\|{1,2}|&{1,2})/'), ['priority' => 0]),

            'string.regex' => [
                new OpenRule(new RegexMatcher('#(?>[\[=(?:+,!]|^|return|=>|&&|\|\|)\s*(/).*?/#m'), [
                    'context' => ['!comment', '!string']
                ]),
                new Rule(new RegexMatcher('#\/.*(/[gimuy]{0,5})#m'), [
                    'priority' => 1,
                    'factory'  => new TokenFactory(ContextualToken::class),
                    'context'  => ['!operator.escape', 'string.regex']
                ])
            ],

            'variable' => new Rule(new RegexMatcher('/\b(?<!\.)(' . self::IDENTIFIER . ':?)/iu'), [
                'priority' => -1,
                'enabled'  => $this->variables
            ]),

            'variable.property' => [
                new Rule(new RegexMatcher('/(?=[\w)\]]\s*\.([a-z_]\w*))/i'), [
                    'priority' => -2
                ]),
                new Rule(new RegexMatcher('/(\w+)\s*:/si'), [
                    'context' => ['meta.json', '!comment', '!string']
                ]),
            ],

            'meta.json' => new Rule(new RegexMatcher('/(?<=[=(,])\s*(\{(?>[^{}]|(?1))+\})/m'), [
                'factory' => new TokenFactory(MetaToken::class)
            ])
        ]);
    }

    public function getIdentifier()
    {
        return 'javascript';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['js', 'jscript', 'javascript'],
            'mime'      => ['application/javascript', 'application/x-javascript', 'text/x-javascript', 'text/javascript', 'application/json'],
            'extension' => ['*.js', '*.jsx'],
        ];
    }
}
