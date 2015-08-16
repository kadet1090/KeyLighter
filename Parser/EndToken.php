<?php
/**
 * Created by PhpStorm.
 * User: k_don
 * Date: 16.08.2015
 * Time: 22:00
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