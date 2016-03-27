<?php
/**
 * Highlighter
 *
 * Copyright (C) 2016, Some right reserved.
 *
 * @author Kacper "Kadet" Donat <kacper@kadet.net>
 *
 * Contact with author:
 * Xmpp:   me@kadet.net
 * E-mail: contact@kadet.net
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter\Formatter;

use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\Tokens;

/**
 * Class HtmlFormatter
 *
 * @package Kadet\Highlighter\Formatter
 */
class HtmlFormatter implements FormatterInterface
{
    public function format(Tokens $tokens)
    {
        $source = $tokens->getSource();

        $result = '';
        $last   = 0;

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
