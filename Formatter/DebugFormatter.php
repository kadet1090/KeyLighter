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

namespace Kadet\Highlighter\Formatter;

use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\Tokens;
use Kadet\Highlighter\Utils\Console;
use Kadet\Highlighter\Utils\StringHelper;

/**
 * Class CliFormatter
 *
 * @package Kadet\Highlighter\Formatter
 */
class DebugFormatter extends CliFormatter implements FormatterInterface
{
    private $_styles;

    public function __construct(bool $styles = false)
    {
        $this->_styles = $styles ?: include __DIR__ . '/../Styles/Cli/Default.php';
    }

    public function format(Tokens $tokens, $leveled = true)
    {
        $source = $tokens->getSource();

        $result = '';
        $last   = 0;

        $level = 0;

        /** @var Token $token */
        foreach ($tokens as $token) {
            $pos = StringHelper::positionToLine($source, $token->pos);

            if ($token->isStart()) {
                if ($leveled) {
                    $result .= str_repeat('    ', $level);
                }

                $result .=
                    Console::styled(['color' => 'yellow'], 'Open  ') .
                    '[' . $pos['line'] . ':' . $pos['pos'] . '] ' .
                    Console::styled(['bold' => true], $token->name) . ' ' .
                    Console::styled(['color' => 'blue'], get_class($token)) . ' ' .
                    Console::styled(['color' => 'light green'], '(' . (isset($token->rule->language) ? $token->rule->language->getIdentifier() : 'none') . ')') .
                    PHP_EOL;

                $level++;

                $result .= Console::styled(
                    self::getColor($token->name),
                    ($leveled ? str_repeat('    ', $level) : null) .
                    implode(
                        PHP_EOL . ($leveled ? str_repeat('    ', $level) : null),
                        explode(PHP_EOL, $this->escape(substr($source, $token->pos, $token->getLength())))
                    ) .
                    PHP_EOL
                );
            } else {
                $level--;

                if ($leveled) {
                    $result .= str_repeat('    ', $level);
                }

                $result .=
                    Console::styled(['color' => 'red'], 'Close ') .
                    '[' . $pos['line'] . ':' . $pos['pos'] . '] ' .
                    Console::styled(['bold' => true], $token->name) . ' ' .
                    Console::styled(['color' => 'blue'], get_class($token)) .
                    PHP_EOL;
            }

            $last = $token->pos;
        }
        $result .= $this->escape(substr($source, $last)) . Console::reset();

        return $result;
    }

    private function escape($string)
    {
        return str_replace(["\r", "\n"], ['\r', '\n' . PHP_EOL], $string);
    }

    public function getColor($token)
    {
        do {
            if (isset($this->_styles[$token])) {
                return $this->_styles[$token];
            }

            $token = explode('.', $token);
            array_pop($token);
            $token = implode('.', $token);
        } while (!empty($token));

        return [];
    }
}
