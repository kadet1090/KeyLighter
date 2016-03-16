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


use Kadet\Highlighter\Language\Language;

class ContextualToken extends Token
{
    public function __construct($options)
    {
        parent::__construct($options);
    }

    protected function validate(Language $language, $context)
    {
        if($language !== $this->getRule()->language) {
            $this->setValid(false);
            return false;
        }

        $start = !in_array($this->name, $context, false);

        if ($start) {
            if (!$this->getRule()->validateContext($context)) {
                $this->setValid(false);
            } else {
                $this->_valid = true;
                $this->_end->_valid = false;
            }
        } else {
            if (!$this->getRule()->validateContext($context, [$this->name => Rule::CONTEXT_IN])) {
                $this->setValid(false);
            } else {
                $this->_valid = false;
                $this->_end->_valid = true;
            }
        }

        $this->_end->_start = false;
        $this->_end = false;

        return true;
    }
}