<?php
/**
 * Highlighter
 *
 * Copyright (C) 2016, Some right reserved.
 *
 * @author Kacper "Kadet" Donat <kacper@kadet.net>
 * @author Olgierd Grzyb <kontakt@olgierd.me>
 *
 * Contact with author:
 * Xmpp:   me@kadet.net
 * E-mail: contact@kadet.net
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter\Formatter;

use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Utils\ArrayHelper;

/**
 * Class LatexFormatter
 *
 * @package Kadet\Highlighter\Formatter
 */
class LatexFormatter extends AbstractFormatter implements FormatterInterface
{
    private $_styles;

    /**
     * LatexFormatter constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        parent::__construct(array_replace_recursive([
            'styles' => include __DIR__.'/../Styles/Cli/Default.php'
        ], $options));

        $this->_styles = $this->_options['styles'];
    }

    protected function token(Token $token)
    {
        list($openTag, $closeTag) = $this->getOpenCloseTags($token);

        return $token->isStart() ? $openTag : $closeTag;
    }

    protected function content($token)
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

        if (ArrayHelper::get($style, 'bold', false)) {
            $openTag .= '\\textbf{';
            $closeTag .= '}';
        }

        if (ArrayHelper::get($style, 'italic', false)) {
            $openTag .= '\\textsl{';
            $closeTag .= '}';
        }

        if (ArrayHelper::get($style, 'underline', false)) {
            $openTag .= '\\underline{';
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
