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


class TokenIterator extends \ArrayIterator
{
    private $_source;

    /**
     * TokenIterator constructor.
     *
     * @param array  $array
     * @param string $source
     * @param int    $flags
     */
    public function __construct(array $array, $source, $flags = 0)
    {
        $this->_source = $source;

        parent::__construct($array, $flags);
    }

    public function getSource()
    {
        return $this->_source;
    }

    public function getTokens($offset = null) {
        /** @var Token[] $result */
        $result = $this->getArrayCopy();

        // Array map would be cool, but is a lot slower
        if($offset) {
            foreach ($result as $item) {
                $item->pos += $offset;
            }
        }

        return $result;
    }

    public function sort() {
        $this->uasort('\Kadet\Highlighter\Parser\Token::compare');
    }
}