<?php
/**
 * Highlighter
 *
 * Copyright (C) 2015, Some right reserved.
 * @author Kacper "Kadet" Donat <kadet1090@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/legalcode CC BY-SA
 *
 * Contact with author:
 * Xmpp: kadet@jid.pl
 * E-mail: kadet1090@gmail.com
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter\Formatter;

use Kadet\Highlighter\Parser\Token;
use Kadet\Highlighter\Parser\TokenList\TokenListInterface;
use Kadet\Highlighter\Utils\ArrayHelper;

/**
 * Class CliFormatter
 * @package Kadet\Highlighter\Formatter
 *
 * @todo: write it better
 */
class CliFormatter implements FormatterInterface
{
    private static $_colors = [
        'comment' => '37',
        'comment.docblock' => '90',
        'variable' => '34',
        'variable.*' => '94',
        'keyword' => '33',
        'operator' => '33',
        'string' => '32',
        'constant' => '35',
        'annotation' => '33',
        'number' => '95',
        'symbol' => '1',
        'tag' => '90',
    ];

    private $_stack = [];
    private $_current;

    public function format($source, TokenListInterface $tokens)
    {
        $result = '';
        $last = 0;

        /** @var Token $token */
        foreach ($tokens as $token) {
            $result .= substr($source, $last, $token->pos - $last);

            if (($color = self::getColor($token->name)) !== null) {
                $result .= $this->_color($token->isStart() ? $color : null);
            }

            $last = $token->pos;
        }
        $result .= substr($source, $last);

        return $result;
    }

    private function _color($color = null)
    {
        if ($color !== null) {
            if ($this->_current !== null) {
                $this->_stack[] = $this->_current;
            }

            $this->_current = $color;
        } else {
            $this->_current = count($this->_stack) > 0 ? array_pop($this->_stack) : '0';
        }

        return "\033[{$this->_current}m";
    }

    public static function getColor($token)
    {
        do {
            $colors = ArrayHelper::filterByKey(self::$_colors, function ($key) use ($token) {
                return fnmatch($key, $token);
            });

            if (!empty($colors)) {
                usort($colors, function($a, $b) {
                    $a = strlen($a);
                    $b = strlen($b);

                    return ($a < $b ? -1 : (int)($a > $b));
                });

                return end($colors);
            }

            $token = explode('.', $token);
            array_pop($token);
            $token = implode('.', $token);
        } while (!empty($token));

        return null;
    }
}