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

namespace Kadet\Highlighter\Language\Css;

use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Parser\Rule;

class Scss extends PreProcessor
{
    /**
     * Tokenization rules
     *
     * @return \Kadet\Highlighter\Parser\Rule[]|\Kadet\Highlighter\Parser\Rule[][]
     */
    public function setupRules()
    {
        parent::setupRules();

        $this->removeRule('symbol.selector.tag');
        $this->addRule('symbol.selector.tag', new Rule(new RegexMatcher('/(?>[\s{};]|^)(?=(\w+)[^;]*\{)/m'), [
            'context' => ['!symbol', '!string', '!number']
        ]));

        $this->addRule('variable', new Rule(new RegexMatcher('/(\$[\w-]+)/'), ['context' => $this->everywhere()]));
    }

    public function getIdentifier()
    {
        return 'scss';
    }
}
