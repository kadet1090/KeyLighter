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


use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Matcher\WordMatcher;
use Kadet\Highlighter\Parser\Rule;

class Python extends Language
{

    /**
     * Tokenization rules setup
     */
    public function setupRules()
    {
        $this->rules->addMany([
            'keyword' => new Rule(new WordMatcher([
                'and', 'del', 'from', 'not', 'while', 'as', 'elif', 'global', 'or', 'with', 'assert', 'else', 'if',
                'pass', 'yield', 'break', 'except', 'import', 'print', 'class', 'exec', 'in', 'raise', 'continue',
                'finally', 'is', 'return', 'def', 'for', 'lambda', 'try',
            ])),
            
            'call' => new Rule(new RegexMatcher('/([a-z_]\w*)\s*\(/i'), ['priority' => 2]),
        ]);
    }

    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'python';
    }
}
