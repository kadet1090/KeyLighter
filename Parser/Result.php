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


use Kadet\Highlighter\Parser\Token\MetaToken;

class Result extends \ArrayObject implements Tokens
{
    private $_source;

    public function __construct($source, $input = [])
    {
        $this->_source = $source;
        parent::__construct($input, 0, \ArrayIterator::class);
    }

    public function getSource()
    {
        return $this->_source;
    }

    public function append($value)
    {
        if(!$value instanceof MetaToken) {
            parent::append($value);
        }
    }

    public function merge($tokens)
    {
        foreach ($tokens as $token) {
            $this->append($token);
        }
    }
}