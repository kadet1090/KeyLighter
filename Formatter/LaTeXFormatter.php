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
use Kadet\Highlighter\Utils\ArrayHelper;

/**
 * Class LateXFormatter
 *
 * @package Kadet\Highlighter\Formatter
 */
class LaTeXFormatter implements FormatterInterface
{
    private $_styles;

    public function __construct($styles = false)
    {
        $this->_styles = $styles ?: include __DIR__.'/../Styles/LaTeX/Default.php';
    }

    public function format(Tokens $tokens)
    {
        $source = $tokens->getSource();

        $result = '';
        $last   = 0;

        /** @var Token $token */
        foreach ($tokens as $token) {
            $result .= sprintf(
                '%s%s',
                $this->escape(substr($source, $last, $token->pos - $last)),
                $token->isStart() ? $this->getOpenTag($token) : $this->getCloseTag($token));

            $last = $token->pos;
        }
        $result .= substr($source, $last);

        return $result;
    }

    protected function escape($token)
    {
        $replace = [
            '\\' => '\\textbackslash{}',
            '{' => '\\{',
            '}' => '\\}',
            // When there is a \ in the source, it gets translated to
            // \textasciibackslash{}, but then the { and } are escaped to \{
            // and \}. This substitution reverts this.
            '\\textbackslash\\{\\}' => '\\textbackslash{}',
            '%' => '\\%',
            '_' => '\\_',
            '^' => '\\textasciicircum{}',
            '~' => '\\textasciitilde{}',
            '$' => '\\$',
            '&' => '\\&',
            '<' => '\\textless{}',
            '>' => '\\textgreater{}',
            '>' => '\\textgreater{}',
            // '"' => '\\textquotedbl{}',
            // '\'' => '\\char13{}',
        ];

        // We can do just with a simple str_replace() because PHP promises to
        // process them sequentially:
        // https://secure.php.net/manual/en/function.str-replace.php#refsect1-function.str-replace-parameters
        return str_replace(
            array_keys($replace),
            array_values($replace),
            $token
        );
    }

    protected function getOpenTag($token)
    {
        $result = '';
        $style = $this->getStyle($token);

        if (($color = ArrayHelper::resolve($style, 'color', 'default')) !== 'default') {
            $result = sprintf('\\textcolor{%s}{%s', $style['color'], $result);
        }
        if (ArrayHelper::resolve($style, 'bold', false)) {
            $result = sprintf('\\textbf{%s', $result);
        }
        if (ArrayHelper::resolve($style, 'italic', false)) {
            $result = sprintf('\\textit{%s', $result);
        }

        return $result;
    }

    protected function getCloseTag($token)
    {
        return str_repeat('}', count($this->getStyle($token)));
    }

    protected function getStyle($token)
    {
        return ArrayHelper::resolve($this->_styles, $token->name, []);
    }
}
