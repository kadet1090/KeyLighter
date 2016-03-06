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
use Kadet\Highlighter\Parser\Token;

class LatexLanguage extends Language
{
    protected static $mathEnvironments = ['align', 'equation', 'math'];

    /**
     * Tokenization rules definition
     *
     * @return array
     */
    public function getRules()
    {
        return [
            'call.symbol' => new Rule(new RegexMatcher('/(\\\\\w+)/'), ['context' => ['!!'], 'priority' => -1]),
            /*'string' => [
                new OpenRule(new SubStringMatcher('{'), ['context' => ['!!'], 'inside' => true, 'priority' => 0]),
                new CloseRule(new SubStringMatcher('}'), ['inside' => true, 'priority' => -2])
            ],*/

            'string.math' => [
                new Rule(new RegexMatcher('/((\${1,2}).*?\2)/s')),
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

            'operator' => new Rule(new WordMatcher(['*', '&', '\\\\'], ['separated' => false]), ['context' => ['!!']]),

            'comment' => new Rule(new CommentMatcher(['%'], [])),
        ];
    }

    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'latex';
    }
}