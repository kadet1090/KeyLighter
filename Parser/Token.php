<?php
/**
 * Created by PhpStorm.
 * User: k_don
 * Date: 16.08.2015
 * Time: 22:01
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
}