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

namespace Kadet\Highlighter\Parser\Token;

use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Utils\Helper;
use Kadet\Highlighter\Utils\StringHelper;

class Token
{
    const NAME  = null;
    const START = 0x1;
    const END   = 0x2;

    protected static $_id = 0;

    public $pos;
    public $name;
    public $index = 1;

    /**
     * @var Token|null|false
     */
    protected $_end;

    /**
     * @var Token|null|false
     */
    protected $_start;

    /** @var Rule */
    protected $_rule;

    protected $_valid;
    protected $_length;

    public $id;

    /**
     * Token constructor.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
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

        if (isset($options['rule'])) {
            $this->setRule($options['rule']);
        } else {
            $this->setRule(new Rule());
        }

        if (isset($options['end'])) {
            $this->setEnd($options['end']);
        }

        $this->id = ++self::$_id;
    }

    public static function compare(Token $a, Token $b)
    {
        $multiplier = $a->isEnd() ? -1 : 1;

        if (($a->isStart() && $b->isEnd()) || ($a->isEnd() && $b->isStart())) {
            if ($a->getStart() == $b) {
                return 1;
            } elseif ($a->getEnd() == $b) {
                return -1;
            } else {
                return $multiplier;
            }
        } elseif (($rule = Helper::cmp($b->_rule->priority, $a->_rule->priority)) !== 0) {
            return $multiplier*$rule;
        } elseif (($rule = Helper::cmp($b->index, $a->index)) !== 0) {
            return $multiplier*$rule;
        } else {
            return $multiplier*($a->id < $b->id ? -1 : 1);
        }
    }

    public function isStart()
    {
        return $this->_start === null;
    }

    public function isEnd()
    {
        return $this->_end === null;
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
            $language === $this->_rule->language &&
            $this->_rule->validate($context, $this->isEnd() ? [$this->name => Rule::CONTEXT_IN] : [])
        );
    }

    public function setValid($valid = true)
    {
        $this->_valid = $valid;

        if ($this->_end) {
            $this->_end->_valid = $this->_valid;
        } elseif ($this->_start) {
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
     * @param Token|null|false $start
     */
    public function setStart($start = null)
    {
        $this->_end   = null;
        $this->_start = $start;

        if ($start instanceof Token) {
            $start->_end = $this;
        }
    }

    /**
     * @return Token|null
     */
    public function getEnd()
    {
        return $this->_end;
    }

    /**
     * @param Token|null|false $end
     */
    public function setEnd($end = null)
    {
        $this->_start  = null;
        $this->_end    = $end;
        $this->_length = null;

        if ($end instanceof Token) {
            $end->_start = $this;
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

        return $this->_length;
    }

    /**
     * @codeCoverageIgnore
     */
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

    public function __get($name)
    {
        return $this->getRule()->$name;
    }
}
