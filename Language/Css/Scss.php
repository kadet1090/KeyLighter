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
use Kadet\Highlighter\Parser\Validator\Validator;

class Scss extends Css
{
    /**
     * Tokenization rules
     *
     * @return \Kadet\Highlighter\Parser\Rule[]|\Kadet\Highlighter\Parser\Rule[][]
     */
    public function getRules()
    {
        $rules                        = parent::getRules();
        $rules['symbol.selector.tag'] = new Rule(new RegexMatcher('/(?>[\s{};]|^)(?=(\w+).*\{)/m'), [
            'context' => Validator::everywhere()
        ]);

        $rules['symbol.selector.class']->setContext(['!symbol', '!string', '!number']);
        $rules['symbol.selector.class.pseudo']->setContext(['!symbol', '!string', '!number']);
        $rules['symbol.selector.id']->setContext(['!symbol', '!string']);
        $rules['constant.color']->setContext(['!string', '!symbol']);

        $rules['variable']      = new Rule(new RegexMatcher('/(\$[\w-]+)/'), ['context' => Validator::everywhere()]);
        $rules['operator.self'] = new Rule(new SubStringMatcher('&'), ['context' => Validator::everywhere()]);

        $rules['comment'] = [
            $rules['comment'],
            new Rule(new CommentMatcher(['//'], []), ['context' => Validator::everywhere()])
        ];

        return $rules;
    }

    public function getIdentifier()
    {
        return 'scss';
    }
}
