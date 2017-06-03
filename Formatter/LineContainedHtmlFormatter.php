<?php


namespace Kadet\Highlighter\Formatter;


use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\Tokens;

class LineContainedHtmlFormatter extends HtmlFormatter implements FormatterInterface
{
    public function format(Tokens $tokens)
    {
        $source = $tokens->getSource();

        $result = '';
        $last   = 0;

        $stack = [];

        /** @var Token $token */
        foreach ($tokens as $token) {
            $result .= preg_replace(
                '/\R/',
                str_repeat($this->getCloseTag(), count($stack))."\n".implode('', $stack),
                htmlspecialchars(substr($source, $last, $token->pos - $last))
            );

            if($token->isStart()) {
                $result .= $stack[] = $this->getOpenTag($token);
            } else {
                array_pop($stack);
                $result .= $this->getCloseTag();
            }

            $last = $token->pos;
        }
        $result .= substr($source, $last);

        return $result;
    }
}