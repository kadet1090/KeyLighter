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

use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Parser\Token\Token;

class Context
{
    public $stack = [];
    public $language;

    public function __construct(Language $language = null)
    {
        $this->language = $language;
    }

    public function find($needle)
    {
        foreach (array_reverse($this->stack, true) as $id => $name) {
            if ($name === $needle) {
                return $id;
            }
        }

        return false;
    }

    public function push(Token $token)
    {
        $this->stack[$token->id] = $token->name;
    }

    public function pop(Token $token)
    {
        unset($this->stack[$token->id]);
    }

    public function has($name)
    {
        return in_array($name, $this->stack, true);
    }

    public static function fromArray(array $array, Language $language = null)
    {
        $context = new Context($language);
        $context->stack = $array;
        return $context;
    }
}
