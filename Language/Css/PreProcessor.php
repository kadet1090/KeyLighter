<?php

declare(strict_types=1);

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
     */
    public function setupRules()
    {
        parent::setupRules();

        $this->rules->rule('symbol.selector.class')->setContext($this->outside());
        $this->rules->rule('symbol.selector.tag')->setContext($this->outside());
        $this->rules->rule('symbol.selector.class.pseudo')->setContext($this->outside());
        $this->rules->rule('symbol.selector.id')->setContext($this->outside());

        $this->rules->rule('constant.color')->setContext(['!string', '!symbol', '!comment']);
        $this->rules->rule('number')->setContext(['!comment', '!symbol', '!constant', '!string', '!variable']);
        $this->rules->rule('call')->setContext(['!comment', '!symbol', '!constant', '!string']);

        $this->rules->add('operator.self', new Rule(new SubStringMatcher('&'), ['context' => $this->everywhere()]));

        $this->rules->add(
            'comment.multiline',
            new Rule(new CommentMatcher(['//'], []), ['context' => $this->rules->rule('comment')->validator])
        );
    }

    protected function outside()
    {
        return new Validator(['!symbol', '!string', '!number', '!comment', '!constant']);
    }
}
