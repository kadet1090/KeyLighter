<?php
/**
 * Highlighter
 *1
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
use Kadet\Highlighter\Parser\CloseRule;
use Kadet\Highlighter\Parser\Token\ContextualToken;
use Kadet\Highlighter\Parser\OpenRule;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\TokenFactory;

class Xml extends Language
{
    const IDENTIFIER = '(?P<namespace>[\w\.-]+:)?(?P<name>[\w\.-]+)';

    /**
     * Tokenization rules
     *
     * @return \Kadet\Highlighter\Parser\Rule[]|\Kadet\Highlighter\Parser\Rule[][]
     */
    public function setupRules()
    {
        $this->addRules([
            'tag.open'  => [
                new OpenRule(new RegexMatcher('/(<\w)/'), ['context' => ['!tag', '!comment']]),
                new CloseRule(new SubStringMatcher('>'), ['context' => ['!string', '!comment']])
            ],
            'tag.close' => new Rule(new RegexMatcher('/(<\/(?:\w+:)?(?:[\w\.]+)>)/')),

            'symbol.tag' => new Rule(new RegexMatcher('/<\\/?' . self::IDENTIFIER . '/', [
                'name'      => Token::NAME,
                'namespace' => '$.namespace'
            ]), ['context' => ['tag', '!string']]),

            'symbol.attribute' => new Rule(new RegexMatcher('/' . self::IDENTIFIER . '=/', [
                'name'      => Token::NAME,
                'namespace' => '$.namespace'
            ]), ['context' => ['tag', '!string']]),

            'string.single' => new Rule(new SubStringMatcher('\''), [
                'context' => ['tag'],
                'factory' => new TokenFactory(ContextualToken::class),
            ]),

            'string.double' => new Rule(new SubStringMatcher('"'), [
                'context' => ['tag'],
                'factory' => new TokenFactory(ContextualToken::class),
            ]),

            'comment' => new Rule(new CommentMatcher([], [['<!--', '-->']])),

            'constant.entity' => new Rule(new RegexMatcher('/(&[a-z]+;)/si')),
        ]);
    }

    /** {@inheritdoc} */
    public function getIdentifier()
    {
        return 'xml';
    }
}
