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
use Kadet\Highlighter\Parser\Token\MetaToken;
use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\Highlighter\Parser\Validator\Validator;

class Sass extends Scss
{

    /**
     * Tokenization rules
     */
    public function setupRules()
    {
        parent::setupRules();

        $this->rules->remove('meta.declaration');
        $this->rules->remove('meta.declaration.media');

        $this->rules->add('meta.selector', new Rule(new RegexMatcher('/(?=(?:\n+|^)(\h*)([^\h].*)\n+\1\h+)/', [
            2 => Token::NAME
        ]), [
            'context'  => Validator::everywhere(),
            'priority' => 3,
            'factory'  => new TokenFactory(MetaToken::class)
        ]));

        $this->rules->add('meta.declaration', new Rule(new RegexMatcher('/\n((?:\h+.*?(?>\n|$)+)+)/'), [
            'context'  => Validator::everywhere(),
            'priority' => 2,
            'factory'  => new TokenFactory(MetaToken::class)
        ]));

        $this->rules->add('meta.declaration.media', new Rule(new RegexMatcher('/@media(.*?)\n/'), [
            'context' => Validator::everywhere(),
            'factory' => new TokenFactory(MetaToken::class)
        ]));

        $this->rules->add('symbol.selector.tag', new Rule(new RegexMatcher('/\b([a-z-][\w-]*)/'), [
            'context' => ['meta.selector', '!symbol', '!meta.declaration.media'],
        ]));

        $this->rules->rule('symbol.selector.class')->setContext(['meta.selector']);
        $this->rules->rule('keyword.at-rule')->setContext(['meta.selector']);
        $this->rules->rule('symbol.selector.class.pseudo')->setContext(['meta.selector']);
        $this->rules->rule('symbol.selector.id')->setContext(['meta.selector']);
    }

    public function getIdentifier()
    {
        return 'sass';
    }
}
