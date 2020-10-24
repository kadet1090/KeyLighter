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

namespace Kadet\Highlighter\Parser\Token;

use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Parser\Context;
use Kadet\Highlighter\Parser\Result;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\TokenIterator;
use Kadet\Highlighter\Parser\Validator\Validator;
use Kadet\Highlighter\Utils\Helper;

class Token
{
    const NAME  = null;
    const START = 0x1;
    const END   = 0x2;

    protected static $_id = 0;

    public $pos;
    public $name;
    public $id;
    public $rule;
    public $options;

    # region >>> cache
    /**
     * @var static|null|false
     */
    protected $_end;

    /**
     * @var static|null|false
     */
    protected $_start;

    protected $_valid;
    protected $_length;
    # endregion

    /**
     * Token constructor.
     *
     * @param null  $name
     * @param array $options
     */
    public function __construct($name = null, array $options = [])
    {
        if (isset($options['pos'])) {
            $this->pos = $options['pos'];
        }

        $this->name     = $name;
        $this->rule     = isset($options['rule']) ? $options['rule'] : new Rule();
        $this->options  = $options;

        $this->id = ++self::$_id;
    }

    public function isStart()
    {
        return $this->_start === null;
    }

    public function isEnd()
    {
        return $this->_end === null;
    }

     public function isValid(Context $context)
    {
        if ($this->_valid === null) {
            $this->validate($context);
        }

        return $this->_valid;
    }

    protected function validate(Context $context)
    {
        $this->setValid(
            $context->language === $this->rule->language &&
            $this->rule->validator->validate($context, $this->isEnd() ? [$this->name => Validator::CONTEXT_IN] : [])
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
     * @return Token|null|false
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
            $this->_start->_length = null;
            $start->_end = $this;
        }
    }

    /**
     * @return Token|null|false
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

    public function getLength()
    {
        if ($this->_length === null) {
            $this->_length = !$this->_end ? 0 : $this->_end->pos - $this->pos;
        }

        return $this->_length;
    }

    public function __get($name)
    {
        return $this->rule->$name;
    }

    /**
     * @param Context       $context
     * @param Language      $language
     * @param Result        $result
     * @param TokenIterator $tokens
     *
     * todo: Documentation
     *
     * @return bool Return true to continue processing, false to return already processed tokens.
     */
    public function process(Context $context, Language $language, Result $result, TokenIterator $tokens) {
        if(!$this->isValid($context)) {
            return true;
        }

        return $this->isStart() ?
            $this->processStart($context, $language, $result, $tokens) :
            $this->processEnd($context, $language, $result, $tokens);
    }

    protected function processStart(Context $context, Language $language, Result $result, TokenIterator $tokens) {
        $result->append($this);
        $context->push($this);

        return true;
    }

    protected function processEnd(Context $context, Language $language, Result $result, TokenIterator $tokens) {
        if($this->_start) {
            $context->pop($this->_start);
        } else {
            if (($start = $context->find($this->name)) !== false) {
                $this->setStart($tokens[$start]);

                unset($context->stack[$start]);
            }
        }

        if (!$this->_start instanceof MetaToken) {
            $result->append($this);
        }

        return true;
    }

    public static function compare(Token $a, Token $b)
    {
        $multiplier = $a->isEnd() ? -1 : 1;

        if (($a->isStart() && $b->isEnd()) || ($a->isEnd() && $b->isStart())) {
            if ($a->getStart() == $b) {
                return 1;
            } if ($a->getEnd() == $b) {
                return -1;
            } else {
                return $multiplier;
            }
        } elseif (($rule = Helper::cmp($b->rule->priority, $a->rule->priority)) !== 0) {
            return $multiplier*$rule;
        }  else {
            return $multiplier*($a->id < $b->id ? -1 : 1);
        }
    }
}
