<?php
/**
 * Highlighter
 *
 * Copyright (C) 2015, Some right reserved.
 * @author Kacper "Kadet" Donat <kadet1090@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/legalcode CC BY-SA
 *
 * Contact with author:
 * Xmpp: kadet@jid.pl
 * E-mail: kadet1090@gmail.com
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter\Parser;


class EndToken extends AbstractToken
{
    /**
     * @var AbstractToken
     */
    public $start;

    public function dump() {
        if (!isset($this->start)) {
            return "End #{$this->id}:{$this->pos}";
        }
        return '';
    }

    public function __toString() {
        return $this->dump();
    }
}