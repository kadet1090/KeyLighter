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


use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Matcher\QuoteMatcher;
use Kadet\Highlighter\Matcher\SubStringMatcher;
use Kadet\Highlighter\Parser\CloseRule;
use Kadet\Highlighter\Parser\OpenRule;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\TokenFactory;

class XmlLanguage extends Language
{
    const IDENTIFIER = '(?P<namespace>\w+:)?(\w+)';

    public function getRules()
    {
        return [
            'tag.open' => [
                new OpenRule(new RegexMatcher('/(<\w)/'), ['context' => ['!tag']]),
                new CloseRule(new SubStringMatcher('>'), ['priority' => -1, 'context' => ['!string']])
            ],

            'symbol.tag' => new Rule(new RegexMatcher('/<\\/?' . self::IDENTIFIER . '/'), ['context' => ['tag', '!string']]),
            'symbol.attribute' => new Rule(new RegexMatcher('/' . self::IDENTIFIER . '=/'), ['context' => ['tag', '!string']]),

            'string.single' => new Rule(new SubStringMatcher('\''), [
                'context' => ['tag'],
                'factory' => new TokenFactory('Kadet\\Highlighter\\Parser\\MarkerToken'),
            ]),

            'string.double' => new Rule(new SubStringMatcher('"'), [
                'context' => ['tag'],
                'factory' => new TokenFactory('Kadet\\Highlighter\\Parser\\MarkerToken'),
            ]),

            'tag.close' => new Rule(new RegexMatcher('/(<\/(?:\w+:)?(?:\w+)>)/')),
        ];
    }

    public function getIdentifier()
    {
        return 'xml';
    }
}
