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
use Kadet\Highlighter\Matcher\CommentMatcher;
use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Matcher\SubStringMatcher;
use Kadet\Highlighter\Parser\Rule;

class Scss extends Css
{
    /**
     * @return \Kadet\Highlighter\Parser\Rule[]|\Kadet\Highlighter\Parser\Rule[][]
     */
    public function getRules()
    {
        $rules = parent::getRules();
        $rules['symbol.selector.tag'] = new Rule(new RegexMatcher('/(?>[\s{};]|^)(?=(\w+).*\{)/m'), ['context' => Rule::everywhere()]);
        $rules['symbol.selector.class']->setContext(['!symbol', '!string', '!number']);
        $rules['symbol.selector.class.pseudo']->setContext(['!symbol', '!string', '!number']);
        $rules['symbol.selector.id']->setContext(['!symbol', '!string']);
        $rules['variable'] = new Rule(new RegexMatcher('/(\$[\w-]+)/'), ['context' => Rule::everywhere()]);
        $rules['operator.self'] = new Rule(new SubStringMatcher('&'), ['context' => Rule::everywhere()]);
        $rules['constant.color']->setContext(['!string', '!symbol']);
        $rules['comment'] = [
            $rules['comment'],
            new Rule(new CommentMatcher(['//'], []), ['context' => Rule::everywhere()])
        ];
        return $rules;
    }

    public function getIdentifier()
    {
        return 'scss';
    }
}