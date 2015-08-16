<?php
/**
 * Created by PhpStorm.
 * User: k_don
 * Date: 16.08.2015
 * Time: 21:59
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
}