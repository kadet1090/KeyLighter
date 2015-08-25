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
use Kadet\Highlighter\Matcher\StringMatcher;
use Kadet\Highlighter\Matcher\SubStringMatcher;
use Kadet\Highlighter\Parser\CloseRule;
use Kadet\Highlighter\Parser\OpenRule;
use Kadet\Highlighter\Parser\Rule;

class XmlLanguage extends Language
{
    const TAG_REGEX = '\w+(?::\w+)?';

    public function getRules()
    {
        return [
            'tag.open' => [
                new OpenRule(new RegexMatcher('/(<)\w/')),
                new CloseRule(new SubStringMatcher('>'), ['priority' => -1])
            ],
            'symbol.tag' => new Rule(new RegexMatcher('/<\\/?(' . self::TAG_REGEX . ')/'), ['context' => ['tag']]),
            'symbol.attribute' => new Rule(new RegexMatcher('/(' . self::TAG_REGEX . ')=/'), ['context' => ['tag']]),
            'string' => new Rule(new StringMatcher([
                'single' => "'",
                'double' => '"'
            ]), ['context' => ['tag']]),

            'tag.close' => new Rule(new RegexMatcher('/(<\\/' . self::TAG_REGEX . '>)/')),
        ];
    }

    public function getIdentifier()
    {
        return 'xml';
    }
}