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

class LanguageToken extends Token
{
    public function getLanguage() {
        return $this->getRule()->inject;
    }

    protected function validate(Language $language, $context)
    {
        $this->setValid(
            ($this->isStart() ? (
                $this->_rule->getLanguage() === null ||
                ($language === $this->_rule->getLanguage() && $this->_rule->validateContext($context))
            ) : $language === $this->_rule->getLanguage())
        );
    }
}