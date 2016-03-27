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
use Kadet\Highlighter\Parser\Rule;

class Ini extends Language
{
    /**
     * Tokenization rules
     */
    public function setupRules()
    {
        $this->rules->addMany([
            'comment'        => new Rule(new CommentMatcher([';'], [])),
            'symbol.section' => new Rule(new RegexMatcher('/(\[.*?])/i')),
            'variable'       => new Rule(new RegexMatcher('/([\.\w]+)\s*=/i')),
            'number'         => new Rule(new RegexMatcher('/(-?\d+)/i')),

            'string' => new Rule(new RegexMatcher('/=\h*(.*)/i')),
        ]);
    }

    /** @inheritdoc */
    public function getIdentifier()
    {
        return 'ini';
    }
}
