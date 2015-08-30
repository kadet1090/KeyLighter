<?php
/**
 * Highlighter
 *1
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


class MarkerToken extends Token
{
    private $_type;

    public function __construct($options)
    {
        parent::__construct($options);
        $this->_type = parent::isStart();
    }

    public function isEnd()
    {
        return !$this->_type;
    }

    public function isStart()
    {
        return $this->_type;
    }


    protected function validate($context)
    {
        $start = !in_array($this->name, $context[1], false);

        if ($start) {
            if (!$this->getRule()->validateContext($context)) {
                $this->invalidate();
            } else {
                $this->_valid = true;
                $this->_end->_valid = false;
            }
        } else {
            if (!$this->getRule()->validateContext($context, [$this->name => Rule::CONTEXT_IN])) {
                $this->invalidate();
            } else {
                $this->_valid = false;
                $this->_end->_valid = true;
            }
        }

        $this->_end->_start = null;
        $this->_end = null;
    }
}