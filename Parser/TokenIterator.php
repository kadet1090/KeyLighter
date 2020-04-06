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

namespace Kadet\Highlighter\Parser;

class TokenIterator extends \ArrayIterator implements Tokens
{
    private $_source;
    private $_offset;

    /**
     * TokenIterator constructor.
     *
     * @param array  $array
     * @param string $source
     * @param int    $offset
     * @param int    $flags
     */
    public function __construct(array $array, $source, $offset = 0, $flags = 0)
    {
        $this->_source = $source;
        $this->_offset = $offset;

        parent::__construct($array, $flags);
    }

    public function getSource()
    {
        return $this->_source;
    }

    public function getOffset()
    {
        return $this->_offset;
    }

    public function getTokens()
    {
        return $this->getArrayCopy();
    }
}
