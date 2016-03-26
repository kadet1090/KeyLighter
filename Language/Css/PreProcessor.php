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
use Kadet\Highlighter\Matcher\SubStringMatcher;
use Kadet\Highlighter\Parser\Rule;

abstract class PreProcessor extends Css
{
    /**
     * Tokenization rules
     *
     * @return \Kadet\Highlighter\Parser\Rule[]|\Kadet\Highlighter\Parser\Rule[][]
     */
    public function getRules()
    {
        $rules = parent::getRules();

        $rules['symbol.selector.class']->setContext(['!symbol', '!string', '!number', '!comment']);
        $rules['symbol.selector.tag']->setContext(['!symbol', '!string', '!number', '!comment']);
        $rules['symbol.selector.class.pseudo']->setContext(['!symbol', '!string', '!number', '!comment']);
        $rules['symbol.selector.id']->setContext(['!symbol', '!string', '!constant', '!comment']);
        $rules['constant.color']->setContext(['!string', '!symbol', '!comment']);

        $rules['operator.self'] = new Rule(new SubStringMatcher('&'), ['context' => $this->everywhere()]);
        $rules['number']->setContext(['!comment', '!symbol', '!constant', '!string', '!variable']);
        $rules['call'] ->setContext(['!comment', '!symbol', '!constant', '!string']);

        $rules['comment'] = [
            $rules['comment'],
            new Rule(new CommentMatcher(['//'], []), ['context' => $rules['comment']->validator])
        ];

        return $rules;
    }
}