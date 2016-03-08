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

namespace Kadet\Highlighter\Formatter;

use Kadet\Highlighter\Parser\Token;
use Kadet\Highlighter\Parser\TokenIterator;

/**
 * Class HtmlFormatter
 *
 * @package Kadet\Highlighter\Formatter
 */
class HtmlFormatter implements FormatterInterface
{

    public function format(TokenIterator $tokens)
    {
        $source = $tokens->getSource();

        $result = '';
        $last = 0;

        /** @var Token $token */
        foreach ($tokens as $token) {
            $result .= htmlspecialchars(substr($source, $last, $token->pos - $last));
            $result .= $token->isStart() ? '<span class="' . str_replace('.', ' ', $token->name) . '">' : '</span>';

            $last = $token->pos;
        }
        $result .= substr($source, $last);

        return $result;
    }
}