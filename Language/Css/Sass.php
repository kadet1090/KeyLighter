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
use Kadet\Highlighter\Parser\MetaToken;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token;
use Kadet\Highlighter\Parser\TokenFactory;

class Sass extends Scss
{
    
    public function getRules()
    {
        $rules = parent::getRules();
        $rules['meta.selector'] = new Rule(new RegexMatcher('/(?=(?:\n+|^)(\h*)([^\h].*)\n+\1\h+)/', [
            2 => Token::NAME
        ]), [
            'context' => Rule::everywhere(),
            'priority' => 3,
            'factory' => new TokenFactory(MetaToken::class)
        ]);

        $rules['meta.declaration'] = new Rule(new RegexMatcher('/\n((?:\h+.*?(?>\n|$)+)+)/'), [
            'context' => Rule::everywhere(),
            'priority' => 2,
            'factory' => new TokenFactory(MetaToken::class)
        ]);

        $rules['meta.declaration.media'] = new Rule(new RegexMatcher('/@media(.*?)/'), [
            'context' => Rule::everywhere(),
            'factory' => new TokenFactory(MetaToken::class)
        ]);

        $rules['symbol.selector.tag'] = new Rule(new RegexMatcher('/([\w-]+)/'), [
            'context' => ['meta.selector', '!symbol', '!meta.declaration.media'],
        ]);
        $rules['symbol.selector.class']->setContext(['meta.selector']);
        $rules['symbol.selector.class.pseudo']->setContext(['meta.selector']);
        $rules['symbol.selector.id']->setContext(['meta.selector']);
        $rules['number']->setContext(['!meta.selector', '!symbol', '!constant', '!comment']);
        $rules['keyword.special']->setContext(['meta.selector']);
        return $rules;
    }

    public function getIdentifier()
    {
        return 'sass';
    }
}