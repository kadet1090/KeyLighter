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


class LanguageToken extends Token
{
    public function getLanguage() {
        return $this->getRule()->getLanguage();
    }

    protected function validate($context)
    {
        $this->invalidate(!$this->_rule->validateContext($context));
    }
}