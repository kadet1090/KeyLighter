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
            } elseif ($language === $lang && $this->_rule->validator->validate($context)) {
                $valid = true;
            }
        } else {
            $desired = $this->getLanguage();
            $valid   = $language === $desired && $this->_rule->validator->validate($context);
        }
        $this->setValid($valid);
    }
}
