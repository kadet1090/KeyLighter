<?php


namespace Kadet\Highlighter\Tests\Helpers;


use Kadet\Highlighter\Formatter\FormatterInterface;
use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\Tokens;

class TestFormatter implements FormatterInterface
{
    public function format(Tokens $tokens)
    {
        $source = $tokens->getSource();

        $result = '';
        $last   = 0;

        /** @var Token $token */
        foreach ($tokens as $token) {
            $result .= substr($source, $last, $token->pos - $last);
            $result .= sprintf($token->isStart() ? '{%s}' : '{/%s}', $this->getTokenInfo($token));

            $last = $token->pos;
        }
        $result .= substr($source, $last);

        return $result;
    }

    private function getTokenInfo(Token $token) {
        return sprintf("%s:%s", $token->name, get_class($token));
    }
}