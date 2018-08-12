<?php


namespace Kadet\Highlighter\Formatter;


use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\Tokens;

abstract class AbstractFormatter implements FormatterInterface
{
    public function format(Tokens $tokens)
    {
        $source = $tokens->getSource();

        $result = '';
        $last   = 0;

        /** @var Token $token */
        foreach ($tokens as $token) {
            $result .= $this->content(substr($source, $last, $token->pos - $last));
            $result .= $this->token($token);

            $last = $token->pos;
        }
        $result .= $this->content(substr($source, $last));

        return $result;
    }

    protected function content($text)
    {
        return $text;
    }

    protected abstract function token(Token $token);
}