<?php
/**
 * Highlighter
 *1
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
use Kadet\Highlighter\Parser\Validator\Validator;

class ContextualToken extends Token
{
    public function __construct($options)
    {
        parent::__construct($options);
    }

    protected function validate(Language $language, $context)
    {
        if ($language !== $this->rule->language) {
            $this->setValid(false);

            return false;
        }

        $start = !in_array($this->name, $context, false);

        if ($start) {
            if (!$this->rule->validator->validate($context)) {
                $this->setValid(false);
            } else {
                $this->_valid       = true;
                $this->_end->_valid = false;
            }
        } else {
            if (!$this->rule->validator->validate($context, [ $this->name => Validator::CONTEXT_IN ])) {
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
