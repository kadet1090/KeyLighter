<?php

declare(strict_types=1);

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
use Kadet\Highlighter\Parser\TokenIterator;

/**
 * Class LanguageToken
 *
 * @package Kadet\Highlighter\Parser\Token
 *
 * @property bool     $postProcess True if language is post processed.
 * @property Language $inject
 */
class LanguageToken extends Token
{
    public function __construct($name, $options = [])
    {
        parent::__construct($name, $options);
        if (isset($options['inject'])) {
            $this->inject = $options['inject'];
        }
    }

    public function getLanguage()
    {
        return $this->getStart() ? $this->getStart()->inject : $this->rule->language;
    }

    protected function validate(Context $context)
    {
        $valid = false;

        if ($this->isStart()) {
            $lang = $this->rule->language;
            if ($lang === null && $this->inject !== $context->language) {
                $valid = true;
            } elseif ($context->language === $lang && $this->rule->validator->validate($context)) {
                $valid = true;
            }
        } else {
            $desired = $this->getLanguage();
            $valid   = $context->language === $desired && $this->rule->validator->validate($context);
        }
        $this->setValid($valid);
    }
    

    protected function processStart(Context $context, Language $language, Result $result, TokenIterator $tokens)
    {
        $result->merge($this->inject->parse($tokens));

        return true;
    }

    protected function processEnd(Context $context, Language $language, Result $result, TokenIterator $tokens)
    {
        $this->setStart($result->getStart());

        if ($this->_start->postProcess) {
            $source = substr($tokens->getSource(), $this->_start->pos - $tokens->getOffset(), $this->_start->getLength());
            $tokens = $this->_start->inject->tokenize(
                $source,
                $result->getTokens(),
                $this->_start->pos,
                Language::EMBEDDED_BY_PARENT
            );
            $result->exchangeArray($this->_start->inject->parse($tokens)->getTokens());
        }

        # closing unclosed tokens
        foreach (array_reverse($context->stack, true) as $id => $name) {
            $end = new Token($name, ['pos' => $this->pos]);
            $tokens[$id]->setEnd($end);
            $result->append($end);
        }

        $result->append($this);
        return false;
    }
}
