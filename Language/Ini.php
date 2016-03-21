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
use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Parser\Rule;

class Ini extends Language
{
    /**
     * Tokenization rules
     *
     * @return \Kadet\Highlighter\Parser\Rule[]|\Kadet\Highlighter\Parser\Rule[][]
     */
    public function getRules()
    {
        return [
            'comment'        => new Rule(new CommentMatcher([';'], [])),
            'symbol.section' => new Rule(new RegexMatcher('/(\[[\.\w]+\])/i')),
            'variable'       => new Rule(new RegexMatcher('/([\.\w]+)\s*=/i')),
            'number'         => new Rule(new RegexMatcher('/(-?\d+)/i')),

            'string' => new Rule(new RegexMatcher('/=\h*(.*)/i')),
        ];
    }

    /** @inheritdoc */
    public function getIdentifier()
    {
        return 'ini';
    }
}
