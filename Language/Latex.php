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

class Latex extends Language
{
    protected static $mathEnvironments = ['align', 'equation', 'math'];

    /**
     * Tokenization rules
     *
     * @return \Kadet\Highlighter\Parser\Rule[]|\Kadet\Highlighter\Parser\Rule[][]
     */
    public function setupRules()
    {
        return [
            'call.symbol' => new Rule(new RegexMatcher('/(\\\[a-z]+)/si'), ['context' => Validator::everywhere(), 'priority' => -1]),

            'string.math' => [
                new Rule(new RegexMatcher('/((\${1,2}).*?\2)/s')),
                new Rule(new RegexMatcher('/(\\\\\(.*?\\\\\))/s')),
                new Rule(new RegexMatcher('/(\\\\\[.*?\\\\\])/s')),
                new Rule(
                    new RegexMatcher(
                        '/\\\begin{((?:' . implode('|', self::$mathEnvironments) . ')\*?)}(.*?)\\\end{\1}/s',
                        [2 => Token::NAME]
                    )
                ),
            ],

            'symbol.argument'    => new Rule(new RegexMatcher('/\[(.*?)\]/')),
            'symbol.environment' => new Rule(new RegexMatcher('/\\\(?:begin|end){(.*?)}/')),

            'symbol.label' => new Rule(new RegexMatcher('/\\\(?:label|ref){(.*?)}/')),

            'operator' => [
                new Rule(new WordMatcher(['*', '&', '\\\\'], ['separated' => false]), ['context' => Validator::everywhere(
                )
                ]),
                new Rule(new WordMatcher(['=', '-', '+', '/', '^', '_'], ['separated' => false]), [
                    'context'  => ['string.math'],
                    'priority' => -1
                ]),
            ],
            'comment' => new Rule(new CommentMatcher(['%'], [])),

            'format.bold' => new Rule(new RegexMatcher('/\\\textbf({((?>[^{}]+|(?1))+)})/si', [
                2 => Token::NAME
            ])),
            'format.italics' => new Rule(new RegexMatcher('/\\\textit({((?>[^{}]+|(?1))+)})/si', [
                2 => Token::NAME
            ])),

            # math mode
            'number' => new Rule(new RegexMatcher('/(-?(?:0[0-7]+|0[xX][0-9a-fA-F]+|0b[01]+|[\d,]+))/'), [
                'context'  => ['string.math'],
                'priority' => -2
            ]),
        ];
    }

    /** {@inheritdoc} */
    public function getIdentifier()
    {
        return 'latex';
    }
}
