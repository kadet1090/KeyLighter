<?php


namespace Kadet\Highlighter\Formatter;


use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\Tokens;

abstract class AbstractFormatter implements FormatterInterface
{
    protected $_options = [];
    protected $_line    = 1;

    public function __construct(array $options = [])
    {
        $this->_options = array_replace_recursive([
            'lines' => [
                'enable' => false,
                'start'  => 1,
                'marked' => []
            ]
        ], $options);
    }

    public function format(Tokens $tokens)
    {
        $source = $tokens->getSource();

        $this->_line = (int)$this->_options['lines']['start'];
        $this->_options['lines']['max'] = substr_count($source, '\n') + $this->_options['lines']['start'];

        $result = $this->_options['lines']['enable'] ? $this->formatLineStart($this->_line) : '';
        $last   = 0;

        /** @var Token $token */
        foreach ($tokens as $token) {
            $result .= $this->_content(substr($source, $last, $token->pos - $last));
            $result .= $this->token($token);

            $last = $token->pos;
        }
        $result .= $this->_content(substr($source, $last));

        return $result.($this->_options['lines']['enable'] ? $this->formatLineEnd($this->_line++) : '');
    }

    private function _content($text)
    {
        $content = $this->content($text);

        return $this->_options['lines']['enable'] ? preg_replace_callback('/\R/u', function($feed) {
            return $this->formatLineEnd($this->_line++).$feed[0].$this->formatLineStart($this->_line);
        }, $content) : $content;
    }

    protected function content($text)
    {
        return $text;
    }

    protected function formatLineStart($line)
    {
        return null;
    }

    protected function formatLineEnd($line)
    {
        return null;
    }

    protected abstract function token(Token $token);
}