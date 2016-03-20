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


use Kadet\Highlighter\Language\Css;
use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Matcher\SubStringMatcher;
use Kadet\Highlighter\Parser\Rule;

class Scss extends Css
{
    public function getRules()
    {
        $rules = parent::getRules();
        $rules['symbol.selector.tag'] = new Rule(new RegexMatcher('/(?>[\s{};]|^)(?=(\w+).*\{)/m'), ['context' => ['!!']]);
        $rules['symbol.selector.class']->setContext(['!number']);
        $rules['symbol.selector.class.pseudo']->setContext(['!number']);
        $rules['symbol.selector.id']->setContext(['!number']);
        $rules['variable'] = new Rule(new RegexMatcher('/(\$[\w-]+)/'), ['context' => ['!!']]);
        $rules['operator.self'] = new Rule(new SubStringMatcher('&'), ['context' => ['!!']]);
        return $rules;
    }

    public function getIdentifier()
    {
        return 'scss';
    }
}