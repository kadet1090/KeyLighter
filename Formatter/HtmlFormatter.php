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

/**
 * Class HtmlFormatter
 *
 * @package Kadet\Highlighter\Formatter
 */
class HtmlFormatter extends AbstractFormatter implements FormatterInterface
{
    protected $_prefix = '';
    protected $_tag    = 'span';

    /**
     * HtmlFormatter constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        $options = array_merge([
            'prefix' => 'kl-',
            'tag'    => 'span'
        ], $options);

        $this->_tag    = $options['tag'];
        $this->_prefix = $options['prefix'];
    }

    protected function getOpenTag(Token $token)
    {
        return sprintf(
            '<%s class="%s">',
            $this->_tag, $this->_prefix.str_replace('.', " {$this->_prefix}", $token->name)
        );
    }

    protected function getCloseTag()
    {
        return "</{$this->_tag}>";
    }

    protected function token(Token $token)
    {
        return $token->isStart() ? $this->getOpenTag($token) : $this->getCloseTag();
    }

    protected function content($text)
    {
        return htmlspecialchars($text);
    }
}
