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
use Kadet\Highlighter\Parser\Token\LanguageToken;
use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\Highlighter\Parser\Validator\Validator;

class Shell extends GreedyLanguage
{

    /**
     * Tokenization rules setup
     */
    public function setupRules()
    {
        $this->rules->addMany([
            'call' => new Rule(new RegexMatcher(
                '/(?>(?<![^\\\]\\\)(?<=\n|\(|\||;|`|^|do|if|then|else|\$\(|^\$\s)\s*(\w+))(?!\s*=)/im'
            ), ['priority' => 1, 'context' => ['*none', '*expression']]),

            'comment' => new Rule(new CommentMatcher(['#'])),
            'string' => CommonFeatures::strings(['single' => '\'', 'double' => '"']),

            'keyword' => new Rule(new WordMatcher([
                'if', 'then', 'else', 'elif', 'fi', 'case', 'esac', 'for', 'select', 'while', 'until', 'do', 'done',
                'in', 'function', 'time', 'coproc'
            ]), ['priority' => 3]),

            'variable'  => [
                'assign' => new Rule(new RegexMatcher('/(\w+)\s*=/')),
                new Rule(new RegexMatcher('/(\$\w+)/i'), ['context' => ['*none', '*string.double']])
            ],

            'number'    => new Rule(new RegexMatcher('/(-?(?:0[0-7]+|0[xX][0-9a-fA-F]+|0b[01]+|\d+))/')),
            'delimiter' => new Rule(new RegexMatcher('/^(\$)/m')),

            'symbol.parameter' => new Rule(new RegexMatcher('/\s(-{1,2}\w+=?)\b/i'), [
                'priority' => 0,
                'context'  => ['!string', '!comment', '!call']
            ]),

            'expression' => [
                new Rule(new RegexMatcher('/(?=(\$\(((?>[^$()]+|(?1))+)\)))/x'), [
                    'context' => Validator::everywhere(),
                    'factory' => new TokenFactory(LanguageToken::class),
                    'inject'  => $this
                ]),
            ],

            'operator.escape' => new Rule(new RegexMatcher('/(\\\(?:x[0-9a-fA-F]{1,2}|u\{[0-9a-fA-F]{1,6}\}|[0-7]{1,3}|.))/i'), [
                'context' => ['string']
            ]),
        ]);
    }

    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'shell';
    }

    public static function getAliases()
    {
        return [
            'name'      => ['shell', 'bash', 'zsh', 'sh'],
            'mime'      => ['text/x-shellscript', 'application/x-shellscript'],
            'extension' => ['*.sh', '*.zsh', '*.bash', '*.ebuild', '*.eclass', '*.exheres-0', '*.exlib', '.bashrc', 'bashrc', '.bash_*', 'bash_*', 'PKGBUILD']
        ];
    }
}
