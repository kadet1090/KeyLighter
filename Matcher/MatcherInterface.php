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

namespace Kadet\Highlighter\Matcher;

use Kadet\Highlighter\Parser\TokenFactoryInterface;

interface MatcherInterface
{
    /**
     * Matches all occurrences and returns token list
     *
     * @param string                $source Source to match tokens
     * @param TokenFactoryInterface $factory
     *
     * @return \Iterator
     */
    public function match($source, TokenFactoryInterface $factory);
}
