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


use Kadet\Highlighter\Utils\Helper;

class Token
{
    private static $_autoincrement = 0;

    public $pos;
    public $name;

    /**
     * @var Token
     */
    private $_end;

    /**
     * @var Token
     */
    private $_start;

    private $_rule;
    private $_id;

    protected $_valid = true;

    /**
     * Token constructor.
     */
    public function __construct($options)
    {
        // Name
        if(array_key_exists(0, $options)) {
            $this->name = $options[0];
        }

        if(array_key_exists('id', $options)) {
            $this->_id = $options['id'];
        } else {
            $this->_id = (++self::$_autoincrement);
        }

        if(array_key_exists('pos', $options)) {
            $this->pos = $options['pos'];
        }

        if(array_key_exists('start', $options)) {
            $this->setStart($options['start']);
        }

        if(array_key_exists('end', $options)) {
            $this->setEnd($options['end']);
        }

        if(array_key_exists('length', $options)) {
            $this->setEnd(new Token([
                $this->name, 'pos' => $this->pos + $options['length'], 'id' => $this->_id, 'start' => $this
            ]));
        }
    }

    public static function compare($a, $b)
    {
        if (!($a instanceof Token) || !($b instanceof Token)) {
            throw new \RuntimeException();
        }

        if ($a->pos == $b->pos) {
            if (($a->isStart() && $b->isStart()) || ($a->isEnd() && $b->isEnd())) {
                return Helper::cmp($b->getRule()->getPriority(), $a->getRule()->getPriority());
            }
            return $a->isEnd() ? -1 : 1;
        }

        return ($a->pos > $b->pos) ? 1 : -1;
    }

    public function isValid() {
        return $this->_valid;
    }

    public function invalidate($invalid = true) {
        $this->_valid = !$invalid;

        if ($this->_end !== null) {
            $this->_end->_valid = $this->_valid;
        } elseif ($this->_start !== null) {
            $this->_start->_valid = $this->_valid;
        }
    }

    public function isEnd() {
        return $this->_end == null && !($this->_rule instanceof OpenRule);
    }

    public function isStart() {
        return $this->_start == null && !($this->_rule instanceof CloseRule);
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->_start;
    }

    /**
     * @param Token $start
     */
    public function setStart(Token $start = null)
    {
        $this->_end = null;
        $this->_start = $start;

        if($start != null) {
            $this->_start->_end = $this;
            $this->_id = $start->_id;
        }
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->_end;
    }

    /**
     * @param Token $end
     */
    public function setEnd(Token $end = null)
    {
        $this->_start = null;
        $this->_end = $end;

        if($end != null) {
            $this->_end->_start = $this;
            $this->_end->_id = $this->_id;
        }
    }

    /**
     * @return Rule
     */
    public function getRule()
    {
        return $this->_rule;
    }

    /**
     * @param Rule $rule
     */
    public function setRule(Rule $rule)
    {
        $this->_rule = $rule;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->_id;
    }

    public function getLength() {
        if($this->_end != null) {
            return $this->_end->pos - $this->pos;
        }

        return 0;
    }

    public function dump($text = null) {
        if($this->isStart()) {
            $result = "Start ({$this->name}) #{$this->_id}:$this->pos";
            if ($this->_end !== null) {
                //$result .= " -> End #{$this->_end->_id}:{$this->_end->pos}";
                if ($text !== null) {
                    $result .= '  '.substr($text, $this->pos, $this->_end->pos - $this->pos);
                }
            }
        } else {
            $result = "End ({$this->name}) #{$this->_id}:$this->pos";
            /*if ($this->_start !== null) {
                $result .= " <- End #{$this->_start->_id}:{$this->_start->pos}";
                if ($text !== null) {
                    $result .= '  '.substr($text, $this->_start->pos, $this->pos - $this->_start->pos);
                }
            }*/
        }
        return $result;
    }
}