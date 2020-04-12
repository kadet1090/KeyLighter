<?php

declare(strict_types=1);

/**
 * Highlighter
 *1
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

use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\TokenFactoryInterface;

class SubStringMatcher implements MatcherInterface
{
    private $_substr;

    /**
     * SubstrMatcher constructor.
     *
     * @param $substr
     */
    public function __construct($substr)
    {
        $this->_substr = $substr;
    }


    /**
     * Matches all occurrences and returns token list
     *
     * @param string                $source Source to match tokens
     *
     * @param TokenFactoryInterface $factory
     *
     * @return \Generator
     */
    public function match($source, TokenFactoryInterface $factory)
    {
        $pos = 0;
        $len = strlen($this->_substr);

        while (($pos = strpos($source, $this->_substr, $pos)) !== false) {
            yield $factory->create(Token::NAME, ['pos' => $pos, 'length' => $len]);

            $pos += $len;
        }
    }
}
