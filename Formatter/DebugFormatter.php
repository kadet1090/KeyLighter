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
use Kadet\Highlighter\Utils\StringHelper;

/**
 * Class CliFormatter
 *
 * @package Kadet\Highlighter\Formatter
 */
class DebugFormatter implements FormatterInterface
{
    private $_styles;

    /**
     * CliFormatter constructor.
     *
     * @param $styles
     */
    public function __construct($styles = false)
    {
        $this->_styles = $styles ?: include __DIR__.'/../Styles/Cli/Default.php';
    }

    public function format(TokenIterator $tokens, $leveled = true)
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
                    Console::styled(['color' => 'yellow'], 'Open  ').
                    '['.$pos['line'].':'.$pos['pos'].'] '.
                    Console::styled(['bold' => true], $token->name).' '.
                    Console::styled(['color' => 'blue'], get_class($token)).
                    PHP_EOL;
                
                $level++;

                $result .= Console::styled(self::getColor($token->name),
                    ($leveled ? str_repeat('    ', $level) : null).
                    implode(
                        PHP_EOL.($leveled ? str_repeat('    ', $level) : null),
                        explode(PHP_EOL, substr($source, $token->pos, $token->getLength()))
                    ).
                    PHP_EOL
                );
            } else {
                $level--;

                if ($leveled) {
                    $result .= str_repeat('    ', $level);
                }

                $result .=
                    Console::styled(['color' => 'red'], 'Close ').
                    '['.$pos['line'].':'.$pos['pos'].'] '.
                    Console::styled(['bold' => true], $token->name).
                    PHP_EOL;
            }

            $last = $token->pos;
        }
        $result .= substr($source, $last).Console::reset();

        return $result;
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
