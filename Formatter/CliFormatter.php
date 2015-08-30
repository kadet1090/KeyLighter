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
use Kadet\Highlighter\Parser\TokenList\TokenListInterface;
use Kadet\Highlighter\Utils\Console;

/**
 * Class CliFormatter
 *
 * @package Kadet\Highlighter\Formatter
 */
class CliFormatter implements FormatterInterface
{
    private $_styles;

    /**
     * CliFormatter constructor.
     *
     * @param $styles
     */
    public function __construct($styles = false) {
        $this->_styles = $styles ?: include __DIR__.'/../Styles/Cli/Default.php';
    }

    public function format($source, TokenListInterface $tokens)
    {
        $result = '';
        $last = 0;

        /** @var Token $token */
        foreach ($tokens as $token) {
            $result .= substr($source, $last, $token->pos - $last);

            if (($style = self::getColor($token->name)) !== null) {
                $result .= $token->isStart() ? Console::open($style) : Console::close();
            }

            $last = $token->pos;
        }
        $result .= substr($source, $last);

        return $result;
    }

    public function getColor($token)
    {
        do {
            if(isset($this->_styles[$token])) {
                return $this->_styles[$token];
            }

            $token = explode('.', $token);
            array_pop($token);
            $token = implode('.', $token);
        } while (!empty($token));

        return null;
    }

}