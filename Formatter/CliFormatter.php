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

namespace Kadet\Highlighter\Formatter;

use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\Tokens;
use Kadet\Highlighter\Utils\ArrayHelper;
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
    public function __construct($styles = false)
    {
        $this->_styles = $styles ?: include __DIR__.'/../Styles/Cli/Default.php';
    }

    public function format(Tokens $tokens)
    {
        $source = $tokens->getSource();

        $result = '';
        $last   = 0;

        /** @var Token $token */
        foreach ($tokens as $token) {
            $result .= substr($source, $last, $token->pos - $last);

            if (($style = ArrayHelper::resolve($this->_styles, $token->name)) !== null) {
                $result .= $token->isStart() ? Console::open(is_callable($style) ? $style($token) : $style) : Console::close();
            }

            $last = $token->pos;
        }
        $result .= substr($source, $last).Console::reset();

        return $result;
    }
}
