<?php
/**
 * Created by PhpStorm.
 * User: k_don
 * Date: 16.08.2015
 * Time: 18:33
 */

namespace Kadet\Highlighter\Parser;


use Kadet\Highlighter\Matcher\MatcherInterface;

class Rule
{
    private $_matcher;
    private $_context = [];

    /**
     * @param MatcherInterface $matcher
     * @param array $options
     */
    public function __construct(MatcherInterface $matcher, array $options = [])
    {
        $this->_matcher = $matcher;

        // Default options:
        $options = array_merge([
            'context' => []
        ], $options);

        $this->_context = $options['context'];
    }

    public function match($source) {
        return $this->_matcher->match($source);
    }

    public function validateContext($context) {
        if (empty($this->_context)) {
            return empty($context);
        }

        foreach ($this->_context as $rule) {
            $type = $this->_getType($rule);
            if($type !== 'in') {
                $rule = substr($rule, 1);
            }

            $matching = array_filter($context, function ($a) use ($rule) {
                return (bool)preg_match('/^'.preg_quote($rule).'(?:\\.\\w+)*$/', $a);
            });

            if($type === 'not in') {
                if(!empty($matching)) {
                    return false;
                }
            } elseif ($type === 'in') {
                if(empty($matching)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function _getType($rule) {
        // Possible more types
        switch($rule[0]) {
            case '!': return 'not in';
            case '^': return 'top';
            default:  return 'in';
        }
    }
}