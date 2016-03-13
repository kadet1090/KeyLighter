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
use Kadet\Highlighter\Utils\Console;

/**
 * Class CliFormatter
 *
 * @package Kadet\Highlighter\Formatter
 */
class DebugFormatter implements FormatterInterface
{
    public function format(TokenIterator $tokens, $leveled = true)
    {
        $source = $tokens->getSource();

        $result = '';
        $last = 0;

        $level = 0;

        /** @var Token $token */
        foreach ($tokens as $token) {
            $content = trim(substr($source, $last, $token->pos - $last));
            if(!empty($content)) {
                if($leveled) {
                    $result .= str_repeat('    ', $level);
                }
                $result .= $content.PHP_EOL;
            }

            if ($token->isStart()) {
                if($leveled) {
                    $result .= str_repeat('    ', $level);
                }

                $result .=
                    Console::styled(['color' => 'green'], 'Open: ').
                    Console::styled(['color' => 'yellow'], $token->name).' '.
                    Console::styled(['color' => 'blue'], get_class($token)).
                    PHP_EOL;
                $level++;
            } else {
                $level--;

                if($leveled) {
                    $result .= str_repeat('    ', $level);
                }

                $result .=
                    Console::styled(['color' => 'red'], 'Close: ').
                    Console::styled(['color' => 'yellow'], $token->name).
                    PHP_EOL;
            }

            $last = $token->pos;
        }
        $result .= substr($source, $last).Console::reset();

        return $result;
    }
}