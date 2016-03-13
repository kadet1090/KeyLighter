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
    public function getInjected() {
        return $this->getRule()->inject;
    }

    public function getLanguage() {
        return $this->getStart() !== null ? $this->getStart()->getRule()->inject : $this->getRule()->language;
    }

    protected function validate(Language $language, $context)
    {
        $valid = false;

        if ($this->isStart()) {
            $lang = $this->_rule->language;
            if($lang === null && $this->getInjected() !== $language) {
                $valid = true;
            } elseif($language === $lang && $this->_rule->validateContext($context)) {
                $valid = true;
            }
        } else {
            $desired = $this->getLanguage();
            $valid = $language === $desired && $this->_rule->validateContext($context);
        }
        $this->setValid($valid);
    }
}