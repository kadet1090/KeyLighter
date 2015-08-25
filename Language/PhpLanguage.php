<?php
/**
 * Highlighter
 *
 * Copyright (C) 2015, Some right reserved.
 * @author Kacper "Kadet" Donat <kadet1090@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/legalcode CC BY-SA
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
use Kadet\Highlighter\Matcher\QuoteMatcher;
use Kadet\Highlighter\Matcher\SubStringMatcher;
use Kadet\Highlighter\Matcher\WordMatcher;
use Kadet\Highlighter\Parser\CloseRule;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\OpenRule;
use Kadet\Highlighter\Utils\ArrayHelper;

class PhpLanguage extends Language
{
    public function getRules()
    {
        $rules = [
            'string' => new Rule(new QuoteMatcher([
                'single' => "'",
                'double' => '"'
            ]), ['context' => ['!keyword.escape', '!comment', '!string']]),

            'string.heredoc' => new Rule(new RegexMatcher('/(<<<(\w+)(.*?)\n\2;)/sm'), ['context' => ['!comment']]),
            'string.nowdoc' => new Rule(new RegexMatcher('/(<<<\'(\w+)\'(.*?)\n\2;)/sm'), ['context' => ['!comment']]),

            'variable' => new Rule(new RegexMatcher('/[^\\\](\$[a-z_]\w*)/i'), [
                'context' => ['!string.single', '!comment']
            ]),
            'variable.property' => new Rule(new RegexMatcher('/(?=(?:\w|\)|\])\s*->([a-z_]\w*))/i')),

            'symbol.function' => new Rule(new RegexMatcher('/function ([a-z_]\w+)\s*\(/i')),
            'symbol.class' => [
                new Rule(new RegexMatcher('/(?:class|new|use) ([\w\\\]+)/i')),
                new Rule(new RegexMatcher('/([\w\\\]+)::/i')),
            ],
            'keyword.escape' => new Rule(new RegexMatcher('/(\\\.)/i'), [
                'context' => ['string']
            ]),

            'comment' => new Rule(new CommentMatcher(['//', '#'], [
                'docblock' => ['/**', '*/'],
                //['/* ', '*/'] // FIXME: Normal comments cannot be matched on docs
            ])),
            'annotation' => new Rule(new RegexMatcher('/[\s]+(@[\w-]+)/i'), [
                'context' => ['comment.docblock']
            ]),

            'constant' => new Rule(new WordMatcher([
                '__CLASS__', '__DIR__', '__FILE__', '__FUNCTION__', 'self',
                '__LINE__', '__METHOD__', '__NAMESPACE__', '__TRAIT__', 'false', 'true'
            ])),
            'constant.static' => new Rule(new RegexMatcher('/(?:[\w\\\]+::|const\s+)(\w+)/i')),

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
                'switch', 'throw', 'trait', 'try', 'unset',
                'use', 'var', 'while', 'xor', 'yield', '<?php', '?>'
            ]), ['context' => ['!string', '!variable', '!comment']]),

            'keyword.cast' => new Rule(
                new RegexMatcher('/(\((?:int|integer|bool|boolean|float|double|real|string|array|object|unset)\))/')
            ),

            'number' => new Rule(new RegexMatcher('/(-?(?:0[xbo]?)?\d+)/')),

            'operator.punctuation' => new Rule(new WordMatcher([',', ';'], ['separated' => false]), ['priority' => 0]),
            'operator' => new Rule(new WordMatcher([
                '->', '++', '--', '-', '+', '/', '*', '**', '||', '&&', '^', '%', '&', '@', '!', '|', ':', '.'
            ], ['separated' => false]), ['priority' => 0])
        ];

        return ArrayHelper::rearrange($rules, [
            'symbol.class',
            'string.heredoc',
            'string.nowdoc',
            'constant',
            'constant.static',
            'keyword.escape',
            'keyword.cast',
            'symbol.function',
            'comment',
            'annotation',
            'variable',
            'variable.property',
            'string',
            'keyword',
            'number',
            'operator.punctuation',
            //'operator',
        ]);
    }

    public function getOpenClose() {
        return [
            new OpenRule(new SubStringMatcher('<?php')),
            new CloseRule(new SubStringMatcher('?>'), [
                'context' => ['!string', '!comment'],
                'priority' => 10000,
                'language' => 'php'
            ])
        ];
    }

    public function getIdentifier()
    {
        return 'php';
    }
}