<?php
/**
 * Created by PhpStorm.
 * User: k_don
 * Date: 16.08.2015
 * Time: 13:51
 */

namespace Kadet\Highlighter\Matcher;
use Kadet\Highlighter\Parser\Token;
use Kadet\Highlighter\Utils\String;

/**
 * Class StringMatcher
 * @package Kadet\Highlighter\Matcher
 *
 * Matches all string occurrences with escaped characters.
 */
class StringMatcher implements MatcherInterface
{
    protected $_quotes;

    /**
     * StringMatcher constructor.
     * @param string[] $quotes possible quotes for string
     */
    public function __construct(array $quotes = ['\'', '"'])
    {
        $this->_quotes = $quotes;
    }

    /**
     * Matches all occurrences and returns token list
     *
     * @param string $source Source to match tokens
     *
     * @return array
     */
    public function match($source)
    {
        $tokens = [];
        $pos = 0;
        do {
            if (($start = String::find($source, array_values($this->_quotes), $pos)) === false) {
                break;
            }
            $end = $this->_findClosingQuote($source, $start + 1, $source[$start]);

            $token = new Token(['pos' => $start, 'length' => $end - $start]);
            if(($key = array_search($source[$start], $this->_quotes, false)) !== false) {
                $token->name = $key;
            }

            $tokens[] = $token;

            $pos = $end + 1;
        } while($pos !== false && $end !== false);

        return $tokens;
    }

    protected function _findClosingQuote($source, $pos, $quote)
    {
        do {
            $pos = strpos($source, $quote, $pos);
            if($pos === false) {
                return strlen($source);
            }

            $escapes = 0;
            for($i = $pos - 1; $i > 0; $i--) {
                if ($source[$i] !== '\\') {
                    break;
                }

                $escapes++;
            }

            $pos++;
        } while ($escapes % 2 === 1);

        return $pos;
    }
}