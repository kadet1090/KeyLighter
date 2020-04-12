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

namespace Kadet\Highlighter\Parser;

use Kadet\Highlighter\Parser\Token\Token;

class Result extends \ArrayObject implements Tokens
{
    private $_source;
    private $_start;

    public function __construct($source, Token $start = null)
    {
        $this->_source = $source;
        $this->_start  = $start;

        parent::__construct($start !== null ? [ $start->id => $start ] : [], 0, \ArrayIterator::class);
    }

    public function getSource()
    {
        return $this->_source;
    }

    public function merge($tokens)
    {
        foreach ($tokens as $token) {
            $this->append($token);
        }
    }

    public function getTokens()
    {
        return $this->getArrayCopy();
    }

    public function getStart()
    {
        return $this->_start;
    }
}
