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

namespace Kadet\Highlighter\Language;

use Kadet\Highlighter\Matcher\MatcherInterface;
use Kadet\Highlighter\Matcher\SubStringMatcher;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\ContextualToken;
use Kadet\Highlighter\Parser\TokenFactory;

final class CommonFeatures
{
    private function __construct()
    {
    }

    public static function strings(array $strings, array $options = [])
    {
        return array_map(function ($matcher) use ($options) {
            return new Rule(
                $matcher instanceof MatcherInterface ? $matcher : new SubStringMatcher($matcher),
                array_replace(['factory' => new TokenFactory(ContextualToken::class)], $options)
            );
        }, $strings);
    }
}
