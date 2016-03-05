<?php
/**
 * Highlighter
 *
 * Copyright (C) 2015, Some right reserved.
 *
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
use Kadet\Highlighter\Matcher\QuoteMatcher;
use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Matcher\SubStringMatcher;
use Kadet\Highlighter\Matcher\WordMatcher;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token;
use Kadet\Highlighter\Parser\TokenFactory;

class PowerShellLanguage extends Language
{

    /**
     * Tokenization rules definition
     *
     * @return array
     */
    public function getRules()
    {
        return [
            'string.single' => new Rule(new SubStringMatcher('\''), [
                'context' => ['!keyword.escape', '!comment', '!string'],
                'factory' => new TokenFactory('Kadet\\Highlighter\\Parser\\MarkerToken'),
            ]),

            'string.double' => new Rule(new SubStringMatcher('"'), [
                'context' => ['!keyword.escape', '!comment', '!string'],
                'factory' => new TokenFactory('Kadet\\Highlighter\\Parser\\MarkerToken'),
            ]),

            'variable' => [
                new Rule(new RegexMatcher('/[^\^](\$(?P<namespace>\w+:)?[a-z_]\w*)/i'), [
                    'context' => ['!string.single', '!comment'],
                    'priority' => 0
                ]),
                new Rule(new RegexMatcher('/[^\^](\$\{(?P<namespace>\w+:)?[a-z_]\w*\})/i'), [
                    'context' => ['!string.single', '!comment'],
                    'priority' => 0
                ]),
            ],

            'variable.splat' => new Rule(new RegexMatcher('/[^\^](\@(?P<namespace>\w+:)?[a-z_]\w*)/i'), [
                'context' => ['!string.single', '!comment'],
                'priority' => 0
            ]),

            'variable.special' => new Rule(new RegexMatcher('/(\$(?:\$|\^|\?|_|true|false|null))/i'), [
                'priority' => 5,
                'context' => ['!string.single', '!comment']
            ]),

            'variable.scope' => new Rule(null, ['context' => ['*variable']]),

            'comment' => new Rule(new CommentMatcher(['#'], [['<#', '#>']])),
            'keyword.doc-section' => new Rule(new RegexMatcher('/[\s\n](\.\w+)/i'), [
                'context' => ['comment']
            ]),

            'symbol.dotnet' => new Rule(new RegexMatcher('/\[([\w\.]+(?:\[\])?)\]/si')),

            'annotation' => new Rule(
                new RegexMatcher('/\[([\w\.]+)\s*(?P<arguments>\((?>[^()]+|(?&arguments))*\))\s*\]/si', [
                    1 => Token::NAME,
                    'arguments' => '$.arguments'
                ])
            ),

            'keyword' => new Rule(new WordMatcher([
                'Begin', 'Break', 'Catch', 'Continue', 'Data', 'Do', 'DynamicParam',
                'Else', 'Elseif', 'End', 'Exit', 'Filter', 'Finally', 'For', 'ForEach',
                'From', 'Function', 'If', 'In', 'InlineScript', 'Hidden', 'Parallel', 'Param',
                'Process', 'Return', 'Sequence', 'Switch', 'Throw', 'Trap', 'Try', 'Until', 'While', 'Workflow'
            ])),

            'operator' => new Rule(new RegexMatcher('/(&|\-eq|\-ne|\-gt|\-ge|\-lt|\-le|\-ieq|\-ine|\-igt|\-ige|\-ilt|\-ile|\-ceq|\-cne|\-cgt|\-cge|\-clt|\-cle|\-like|\-notlike|\-match|\-notmatch|\-ilike|\-inotlike|\-imatch|\-inotmatch|\-clike|\-cnotlike|\-cmatch|\-cnotmatch|\-contains|\-notcontains|\-icontains|\-inotcontains|\-ccontains|\-cnotcontains|\-isnot|\-is|\-as|\-replace|\-ireplace|\-creplace|\-and|\-or|\-band|\-bor|\-not|\-bnot|\-f|\-casesensitive|\-exact|\-file|\-regex|\-wildcard)\b/i'), [
                'context' => ['!string', '!comment']
            ]),

            'parameter' => new Rule(new RegexMatcher('/(-\w+:?)\b/i'), [
                'priority' => 0,
                'context' => ['!string', '!comment', '!call']
            ]),

            'operator.punctuation' => new Rule(new WordMatcher([',', ';', '.', '::', '%'], ['separated' => false]), [
                'priority' => 0,
                'context' => ['!string', '!comment']
            ]),

            'number' => [
                new Rule(new RegexMatcher('/(-?(?:0x[0-9a-f]+|\d+)l?(?:kb|mb|gb|tb|pb)?)/i'), [
                    'priority' => 0,
                    'context' => ['!string', '!comment', '!variable']
                ]),
                new Rule(new RegexMatcher('/(-?(?>\d+)?\.\d+(?>d|l)(?>e(?:\+|-)?\d+)?(?:kb|mb|gb|tb|pb)?)/i'), [
                    'priority' => 0,
                    'context' => ['!string', '!comment', '!variable']
                ])
            ],

            'call' => new Rule(new RegexMatcher('/\b(\w+-\w+)\b/i'), ['priority' => 10])
        ];
    }

    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'PowerShell';
    }
}