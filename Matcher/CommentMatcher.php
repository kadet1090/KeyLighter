<?php
/**
 * Created by PhpStorm.
 * User: k_don
 * Date: 16.08.2015
 * Time: 20:01
 */

namespace Kadet\Highlighter\Matcher;


use Kadet\Highlighter\Parser\Token;

class CommentMatcher implements MatcherInterface
{
    private $singleLine = [];
    private $multiLine = [];

    /**
     * CommentMatcher constructor.
     * @param array $singleLine
     * @param array $multiLine
     */
    public function __construct(array $singleLine, array $multiLine)
    {
        $this->singleLine = $singleLine;
        $this->multiLine = $multiLine;
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
        $result = [];
        $all = [];

        foreach ($this->multiLine as $name => $comment) {
            $comment = array_map(function ($e) { return preg_quote($e, '/'); }, $comment);

            $all[] = [$name, "/{$comment[0]}(.*?){$comment[1]}/ms"];
        }

        foreach ($this->singleLine as $name => $comment) {
            $comment = preg_quote($comment, '/');
            $all[] = [$name, "/{$comment}(.*)/"];
        }

        foreach ($all as $comment) {
            $matches = [];

            $name = $comment[0];
            $regex = $comment[1];

            if (preg_match_all($regex, $source, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $match) {
                    $token = new Token(['pos' => $match[1], 'length' => strlen($match[0])]);

                    if (!is_int($name)) {
                        $token->name = $name;
                    }

                    $result[] = $token;
                }
            }
        }

        return $result;
    }
}