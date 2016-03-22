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

/**
 * Class LanguageToken
 *
 * @package Kadet\Highlighter\Parser\Token
 *
 * @property bool $postProcess True if language is post processed.
 */
class LanguageToken extends Token
{
    public function getInjected()
    {
        return $this->getRule()->inject;
    }

    public function getLanguage()
    {
        return $this->getStart() ? $this->getStart()->getRule()->inject : $this->getRule()->language;
    }

    protected function validate(Language $language, $context)
    {
        $valid = false;

        if ($this->isStart()) {
            $lang = $this->_rule->language;
            if ($lang === null && $this->getInjected() !== $language) {
                $valid = true;
            } elseif ($language === $lang && $this->_rule->validate($context)) {
                $valid = true;
            }
        } else {
            $desired = $this->getLanguage();
            $valid   = $language === $desired && $this->_rule->validate($context);
        }
        $this->setValid($valid);
    }
}
