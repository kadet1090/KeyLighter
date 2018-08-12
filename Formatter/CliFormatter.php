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
class CliFormatter extends AbstractFormatter implements FormatterInterface
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
        return parent::format($tokens).Console::reset();
    }

    protected function token(Token $token)
    {
        $style = ArrayHelper::resolve($this->_styles, $token->name);

        if ($style === null) {
            return null;
        }

        return $token->isStart()
            ? Console::open(is_callable($style) ? $style($token) : $style)
            : Console::close();
    }
}
