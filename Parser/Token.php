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


class Token extends AbstractToken
{
    public $length;

    public function split() {
        $start = new StartToken([
            'name' => $this->name,
            'pos'  => $this->pos,
            'rule' => $this->rule,
            'id'   => $this->id
        ]);

        $end = new EndToken([
            'name'  => $this->name,
            'pos'   => $this->pos + $this->length,
            'rule'  => $this->rule,
            'start' => $start,
            'id'    => $this->id
        ]);

        $start->end = $end;

        return [$start, $end];
    }



    public function getLength() {

    }

    public static function pair(StartToken $start, EndToken $end) {

    }
}