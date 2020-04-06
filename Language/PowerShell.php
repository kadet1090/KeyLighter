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
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\Validator\Validator;

class PowerShell extends GreedyLanguage
{

    /**
     * Tokenization rules
     */
    public function setupRules()
    {
        $namespace = '(?:(?P<namespace>\w+):)?';
        $namespaceGroup = [
            1           => Token::NAME,
            'namespace' => '$.scope',
        ];
        $variableRules = new Validator(['!string.single', '!comment', '!variable.special']);

        $this->rules->addMany([
            'string' => CommonFeatures::strings(['single' => '\'', 'double' => '"'], [
                'context' => ['!operator.escape', '!comment', '!string'],
            ]),

            'variable' => [
                new Rule(new RegexMatcher('/(?<!`)(\$' . $namespace . '[a-z_]\w*)/i', $namespaceGroup), [
                    'context'  => $variableRules,
                    'priority' => 0,
                ]),
                new Rule(new RegexMatcher('/[^`](\$\{' . $namespace . '.*?\})/i', $namespaceGroup), [
                    'context'  => $variableRules,
                    'priority' => 0,
                ]),
            ],

            'variable.splat' => new Rule(new RegexMatcher('/[^`](\@' . $namespace . '[a-z_]\w*)/i', $namespaceGroup), [
                'context'  => $variableRules,
                'priority' => 0,
            ]),

            'variable.special' => new Rule(new RegexMatcher('/(\$(?:\$|\^|\?|_|true|false|null))/i'), [
                'priority' => 5,
                'context'  => $variableRules,
            ]),


            'comment'             => new Rule(new CommentMatcher(['#'], [['<#', '#>']])),
            'keyword.doc-section' => new Rule(new RegexMatcher('/(?:\s|\R)(\.\w+)/i'), [
                'context' => ['comment'],
            ]),

            'symbol.dotnet' => new Rule(new RegexMatcher('/(\[[a-z][\w\.]*(?:\[\])?\])/si')),

            'symbol.annotation' => new Rule(
                new RegexMatcher('/\[([\w\.]+)\s*(?P<arguments>\((?>[^()]+|(?&arguments))*\))\s*\]/si', [
                    1           => Token::NAME,
                    'arguments' => '$.arguments',
                ])
            ),

            'keyword' => new Rule(new WordMatcher([
                'Begin', 'Break', 'Catch', 'Continue', 'Data', 'Do', 'DynamicParam',
                'Else', 'Elseif', 'End', 'Exit', 'Filter', 'Finally', 'For', 'ForEach',
                'From', 'Function', 'If', 'In', 'InlineScript', 'Hidden', 'Parallel', 'Param',
                'Process', 'Return', 'Sequence', 'Switch', 'Throw', 'Trap', 'Try', 'Until', 'While', 'Workflow',
            ]), ['priority' => 3]),

            'operator' => new Rule(
                new RegexMatcher('/(&|\-eq|\-ne|\-gt|\-ge|\-lt|\-le|\-ieq|\-ine|\-igt|\-ige|\-ilt|\-ile|\-ceq|\-cne|\-cgt|\-cge|\-clt|\-cle|\-like|\-notlike|\-match|\-notmatch|\-ilike|\-inotlike|\-imatch|\-inotmatch|\-clike|\-cnotlike|\-cmatch|\-cnotmatch|\-contains|\-notcontains|\-icontains|\-inotcontains|\-ccontains|\-cnotcontains|\-isnot|\-is|\-as|\-replace|\-ireplace|\-creplace|\-and|\-or|\-band|\-bor|\-not|\-bnot|\-f|\-casesensitive|\-exact|\-file|\-regex|\-wildcard)\b/i'),
                [
                    'context' => ['!string', '!comment'],
                ]
            ),

            'symbol.parameter' => new Rule(new RegexMatcher('/\s(-\w+:?)\b/i'), [
                'priority' => 0,
                'context'  => ['!string', '!comment', '!call'],
            ]),

            'operator.punctuation' => new Rule(new WordMatcher([',', ';', '.', '::', '%'], ['separated' => false]), [
                'priority' => 0,
                'context'  => ['!string', '!comment', '!call'],
            ]),

            'number' => [
                new Rule(new RegexMatcher('/\b(-?(?:0x[0-9a-f]+|\d+)l?(?:kb|mb|gb|tb|pb)?)\b/i'), [
                    'priority' => 0,
                    'context'  => ['!string', '!comment', '!variable', '!call'],
                ]),
                new Rule(new RegexMatcher('/\b(-?(?>\d+)?\.\d+(?>d|l)(?>e(?:\+|-)?\d+)?(?:kb|mb|gb|tb|pb)?)\b/i'), [
                    'priority' => 0,
                    'context'  => ['!string', '!comment', '!variable', '!call'],
                ]),
            ],

            'call' => new Rule(new RegexMatcher(
                '/(?<![^`]`)(?<=\n|\{|\(|\}|\||=|;|^|function|filter|^PS>)\s*((?:\w+\\\)?\w[\w\.\-]+)/im'
            ), ['priority' => 2]),

            'delimiter.prompt' => new Rule(new RegexMatcher('/^(PS>)/im'), ['priority' => 4]),
        ]);
    }

    /** {@inheritdoc} */
    public function getIdentifier()
    {
        return 'PowerShell';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['powershell', 'posh'],
            'mime'      => ['text/x-powershell', 'application/x-powershell'],
            'extension' => ['*.ps1', '*.psm1', '*.psd1'],
        ];
    }
}
