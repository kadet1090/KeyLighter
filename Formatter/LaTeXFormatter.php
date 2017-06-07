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
            list($openTag, $closeTag) = $this->getOpenCloseTags($token);
            $result .= $this->escape(substr($source, $last, $token->pos - $last));
            $result .= $token->isStart() ? $openTag : $closeTag;

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

    protected function getOpenCloseTags($token)
    {
        $openTag = $closeTag = '';
        $style = $this->getStyle($token);

        if (ArrayHelper::get($style, 'italic', false)) {
            $openTag .= '\\textit{';
            $closeTag .= '}';
        }
        if (ArrayHelper::get($style, 'bold', false)) {
            $openTag .= '\\textbf{';
            $closeTag .= '}';
        }
        if (($color = ArrayHelper::get($style, 'color', 'default')) !== 'default') {
            $openTag .= sprintf('\\textcolor{%s}{', $style['color']);
            $closeTag .= '}';
        }

        return [$openTag, $closeTag];
    }

    protected function getStyle($token)
    {
        return ArrayHelper::resolve($this->_styles, $token->name, []);
    }
}