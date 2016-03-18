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
use Kadet\Highlighter\Matcher\SubStringMatcher;
use Kadet\Highlighter\Matcher\WordMatcher;
use Kadet\Highlighter\Parser\CloseRule;
use Kadet\Highlighter\Parser\ContextualToken;
use Kadet\Highlighter\Parser\OpenRule;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\TokenFactory;

class Css extends Language
{

    /**
     * Tokenization rules definition
     *
     * @return array
     */
    public function getRules()
    {
        $identifier = '[\w-]+';

        return [
            'declaration' => [
                new OpenRule(new SubStringMatcher('{'), ['context' => ['!declaration.media']]),
                new CloseRule(new SubStringMatcher('}')),
            ],

            'declaration.media' => [
                new Rule(new RegexMatcher('/@media(.*?\{)/'), ['context' => ['!!']]),
            ],

            'declaration.rule' => [
                new OpenRule(new SubStringMatcher('('), ['context' => ['declaration.media']]),
                new CloseRule(new SubStringMatcher(')')),
            ],

            'keyword.control-sequence' => new Rule(new RegexMatcher("/(@$identifier)/")),

            'string.single' => new Rule(new SubStringMatcher('\''), [
                'context' => ['!keyword.escape', '!comment', '!string', '!keyword.nowdoc'],
                'factory' => new TokenFactory(ContextualToken::class),
            ]),

            'string.double' => new Rule(new SubStringMatcher('"'), [
                'context' => ['!keyword.escape', '!comment', '!string'],
                'factory' => new TokenFactory(ContextualToken::class),
            ]),

            'symbol.selector.id'    => new Rule(new RegexMatcher("/(#$identifier)/")),
            'symbol.selector.tag'   => new Rule(new RegexMatcher("/(?=(?>\\s|\\/|}|^)(\\w+).*\\{)/")),
            'symbol.selector.class' => new Rule(new RegexMatcher("/(\\.$identifier)/")),

            'symbol.selector.class.pseudo' => new Rule(new RegexMatcher("/(:$identifier)/")),

            'number' => new Rule(new RegexMatcher("/([-+]?[0-9]*\\.?[0-9]+([\\w%]+)?)/"), [
                'context' => ['declaration', '!constant.color']
            ]),
            'constant.property' => new Rule(new RegexMatcher("/($identifier):/"), ['context' => ['declaration']]),

            'call' => new Rule(new RegexMatcher("/($identifier)\\(/"), ['context' => ['!!']]),

            'constant.color' => new Rule(new RegexMatcher("/(#[0-9a-f]{1,6})/i"), [
                'priority' => 2,
                'context' => ['declaration', '!symbol.color']
            ]),

            'operator' => new Rule(new WordMatcher([':', '>', '+', '*', ';', ',', '!important'], ['separated' => false]), [
                'context' => ['!comment']
            ]),

            'comment' => new Rule(new CommentMatcher([], [['/*', '*/']]))
        ];
    }

    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'css';
    }
}