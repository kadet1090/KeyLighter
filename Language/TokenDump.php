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

use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Parser\Rule;

class TokenDump extends Language
{
    public function getRules()
    {
        return [
            'string'  => new Rule(new RegexMatcher("/\x02(.*?)\x03/ms")),
            'keyword' => new Rule(new RegexMatcher("/(?:^|\\n)\\s*(Start|End) /")),
            'symbol'  => new Rule(new RegexMatcher("/(?:^|\\n)\\s*(?:Start|End) \\((.*?)\\)/")),
        ];
    }

    public function getIdentifier()
    {
        return 'token-dump';
    }
}
