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
use Kadet\Highlighter\Parser\Validator\Validator;

abstract class PreProcessor extends Css
{
    /**
     * Tokenization rules
     *
     * @return \Kadet\Highlighter\Parser\Rule[]|\Kadet\Highlighter\Parser\Rule[][]
     */
    public function setupRules()
    {
        parent::setupRules();

        $this->rule('symbol.selector.class')->setContext($this->outside());
        $this->rule('symbol.selector.tag')->setContext($this->outside());
        $this->rule('symbol.selector.class.pseudo')->setContext($this->outside());
        $this->rule('symbol.selector.id')->setContext($this->outside());

        $this->rule('constant.color')->setContext(['!string', '!symbol', '!comment']);
        $this->rule('number')->setContext(['!comment', '!symbol', '!constant', '!string', '!variable']);
        $this->rule('call')->setContext(['!comment', '!symbol', '!constant', '!string']);

        $this->addRule('operator.self', new Rule(new SubStringMatcher('&'), ['context' => $this->everywhere()]));

        $this->addRule(
            'comment.multiline',
            new Rule(new CommentMatcher(['//'], []), ['context' => $this->rule('comment')->validator])
        );
    }

    protected function outside() {
        return new Validator(['!symbol', '!string', '!number', '!comment', '!constant']);
    }
}