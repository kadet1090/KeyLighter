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

    private $_stack;

    /**
     * HtmlFormatter constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $options = array_merge([
            'prefix' => 'kl-',
            'tag'    => 'span'
        ], $options);

        $this->_tag    = $options['tag'];
        $this->_prefix = $options['prefix'];
    }

    public function format(Tokens $tokens)
    {
        $this->_stack = [];
        return parent::format($tokens);
    }

    protected function getOpenTag(Token $token)
    {
        return sprintf(
            '<%s class="%s">',
            $this->_tag,
            $this->_prefix . str_replace('.', " {$this->_prefix}", $token->name)
        );
    }

    protected function getCloseTag()
    {
        return "</{$this->_tag}>";
    }

    protected function token(Token $token)
    {
        if ($token->isStart()) {
            return $this->_stack[] = $this->getOpenTag($token);
        } else {
            array_pop($this->_stack);
            return $this->getCloseTag();
        }
    }

    protected function content($text)
    {
        return htmlspecialchars($text);
    }

    protected function formatLineStart($line): string
    {
        return implode('', $this->_stack);
    }

    protected function formatLineEnd($line): string
    {
        return str_repeat($this->getCloseTag(), count($this->_stack));
    }
}
