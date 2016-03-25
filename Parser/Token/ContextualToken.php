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

namespace Kadet\Highlighter\Parser\Token;

use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Parser\Validator\Validator;

class ContextualToken extends Token
{
    public function __construct($options)
    {
        parent::__construct($options);
    }

    protected function validate(Language $language, $context)
    {
        if ($language !== $this->getRule()->language) {
            $this->setValid(false);

            return false;
        }

        $start = !in_array($this->name, $context, false);

        if ($start) {
            if (!$this->getRule()->validate($context)) {
                $this->setValid(false);
            } else {
                $this->_valid       = true;
                $this->_end->_valid = false;
            }
        } else {
            if (!$this->getRule()->validate($context, [ $this->name => Validator::CONTEXT_IN ])) {
                $this->setValid(false);
            } else {
                $this->_valid       = false;
                $this->_end->_valid = true;
            }
        }

        $this->_end->_start = false;
        $this->_end         = false;

        return true;
    }
}
