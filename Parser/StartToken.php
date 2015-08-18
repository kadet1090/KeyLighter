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


class StartToken extends AbstractToken
{
    /**
     * @var AbstractToken
     */
    public $end;

    public function dump($text = null) {
        $result = "Start ({$this->name}) #{$this->id}:$this->pos";
        if (isset($this->end) && $this->end instanceof AbstractToken) {
            $result .= " -> End #{$this->end->id}:{$this->end->pos}";
            if ($text !== null) {
                $result .= '  '.substr($text, $this->pos, $this->end->pos - $this->pos);
            }
        }
        return $result;
    }

    public function __toString() {
        return $this->dump();
    }

    public function invalidate($invalid = true) {
        parent::invalidate($invalid);
        $this->end->_valid = $this->_valid;
    }
}