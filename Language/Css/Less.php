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

class Less extends PreProcessor
{
    /**
     * Tokenization rules
     *
     * @return \Kadet\Highlighter\Parser\Rule[]|\Kadet\Highlighter\Parser\Rule[][]
     */
    public function setupRules()
    {
        $rules = parent::setupRules();
        $this->addRule('variable', new Rule(new RegexMatcher('/(@[\w-]+)/'), [
            'context'  => ['!comment', '!keyword'],
            'priority' => -1
        ]));

        return $rules;
    }

    public function getIdentifier()
    {
        return 'less';
    }
}