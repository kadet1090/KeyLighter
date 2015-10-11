<?php
/**
 * Highlighter
 *
 * Copyright (C) 2015, Some right reserved.
 *
 * @author Kacper "Kadet" Donat <kadet1090@gmail.com>
 *
 * Contact with author:
 * Xmpp: kadet@jid.pl
 * E-mail: kadet1090@gmail.com
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter\Parser;


use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Utils\Helper;
use Kadet\Highlighter\Utils\StringHelper;

class Token
{
    const NAME = null;

    protected static $_id = 0;

    public $pos;
    public $name;
    public $index = 1;

    /**
     * @var Token
     */
    protected $_end;

    /**
     * @var Token
     */
    protected $_start;

    /** @var Rule */
    protected $_rule;

    protected $_valid;
    protected $_length;

    public $id;

    /**
     * Token constructor.
     */
    public function __construct(array $options)
    {
        // Name
        if (isset($options[0])) {
            $this->name = $options[0];
        }

        if (isset($options['pos'])) {
            $this->pos = $options['pos'];
        }

        if (isset($options['index'])) {
            $this->index = $options['index'];
        }

        if (isset($options['start'])) {
            $this->setStart($options['start']);
        }

        if (isset($options['end'])) {
            $this->setEnd($options['end']);
        }

        $this->id = ++self::$_id;

        if (isset($options['length'])) {
            new static([$this->name, 'pos' => $this->pos + $options['length'], 'start' => $this]);
        }
    }

    public static function compare(Token $a, Token $b)
    {
        if ($a->pos === $b->pos) {
            if (($a->isStart() && $b->isStart()) || ($a->isEnd() && $b->isEnd())) {
                if (($rule = Helper::cmp($b->_rule->getPriority(), $a->_rule->getPriority())) !== 0) {
                    return $rule;
                }

                if (($rule = Helper::cmp($b->index, $a->index)) !== 0) {
                    return $rule;
                }

                return $a->id < $b->id ? -1 : 1;
            }

            return $a->isEnd() ? -1 : 1;
        }

        return ($a->pos > $b->pos) ? 1 : -1;
    }

    public function isStart()
    {
        return $this->_start === null && !($this->_rule instanceof CloseRule);
    }

    public function isEnd()
    {
        return $this->_end === null && !($this->_rule instanceof OpenRule);
    }

    public function isValid(Language $language, $context = null)
    {
        if ($this->_valid === null) {
            $this->validate($language, $context);
        }

        return $this->_valid;
    }

    protected function validate(Language $language, $context)
    {
        $this->setValid(
            $language === $this->_rule->getLanguage() &&
            $this->_rule->validateContext($context, $this->isEnd() ? [$this->name => Rule::CONTEXT_IN] : [])
        );
    }

    public function setValid($valid = true)
    {
        $this->_valid = $valid;

        if ($this->_end !== null) {
            $this->_end->_valid = $this->_valid;
        } elseif ($this->_start !== null) {
            $this->_start->_valid = $this->_valid;
        }
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

        if ($start !== null) {
            $this->_start->_end = $this;
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
        $this->_length = null;

        if ($end !== null) {
            $this->_end->_start = $this;
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

    public function getLength()
    {
        if ($this->_length === null) {
            $this->_length = $this->_end === null ? 0 : $this->_end->pos - $this->pos;
        }

        return 0;
    }

    public function dump($text = null)
    {
        $pos = StringHelper::positionToLine($text, $this->pos);
        $pos = $pos['line'] . ':' . $pos['pos'];

        if ($this->isStart()) {
            $result = "Start ({$this->name}) $pos";
            if ($text !== null && $this->_end !== null) {
                $result .= "  \x02" . substr($text, $this->pos, $this->_end->pos - $this->pos) . "\x03";
            }
        } else {
            $result = "End ({$this->name}) $pos";
        }

        return $result;
    }
}