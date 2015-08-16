<?php
/**
 * Created by PhpStorm.
 * User: k_don
 * Date: 16.08.2015
 * Time: 20:59
 */

namespace Kadet\Highlighter\Language;


use Kadet\Highlighter\Parser\EndToken;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\StartToken;
use Kadet\Highlighter\Parser\Token;

abstract class Language
{
    const START = 1;
    const END = 0;

    private $_tokens = [];
    private $_mapping = [];

    /**
     * @var Rule[]
     */
    private $_rules;

    private $_text;


    /**
     * Parser constructor.
     * @param $text
     */
    public function __construct($text)
    {
        $this->_text = $text;
        $this->_rules = $this->getRules();
    }


    public function tokenize()
    {
        $tokens = [];
        foreach ($this->_rules as $name => $rule) {
            $tokens[$name] = $rule->getMatcher()->match($this->_text);
        }

        $this->_saveTokens($tokens);

        $this->_fixTokens();

        return $this->tokens();
    }

    public abstract function getRules();


    private function _saveTokens($array)
    {
        $this->_tokens = [];
        foreach ($array as $name => $tokens) {
            foreach ($tokens as $token) {
                $token->name = $name . (array_key_exists(0, $token) ? '.' . $token[0] : '');
                $token->rule = $this->_rules[$name];
                $token->id   = count($this->_tokens);

                $this->_mapping[$token->id] = [];

                if ($token instanceof Token) {
                    $this->_tokens = array_merge($this->_tokens, $token->split());
                    $this->_mapping[$token->id] = [$token->id, $token->id + 1];
                } else {
                    $this->_tokens[] = $token;
                    $this->_mapping[$token->id] = [$token->id];
                }
            }
        }

        uasort($this->_tokens, function ($a, $b) {
            if ($a->pos < $b->pos) {
                return -1;
            }

            return (int)($a->pos > $b->pos);
        });
    }

    private function _fixTokens()
    {
        $list = $this->tokens();
        $context = [];

        foreach ($list as $token) {
            /** @var Rule $rule */
            $rule = $token->rule;

            if ($token instanceof StartToken) {
                if ($rule->validateContext($context)) {
                    $context[] = $token->name;
                } else {
                    foreach($this->_mapping[$token->id] as $id) {
                        unset($this->_tokens[$id]);
                    }
                }
            } elseif ($token instanceof EndToken) {
                $context = array_diff($context, [$token->name]);
            }
        }
    }

    public function tokens()
    {
        $list = [];
        foreach ($this->_tokens as $id => $token) {
            if ($token instanceof Token) {
                $list = array_merge($list, $token->split());
            }
        }

        uasort($list, function ($a, $b) {
            if ($a->pos < $b->pos) {
                return -1;
            }

            return (int)($a->pos > $b->pos);
        });

        return $list;
    }

    public function __dumpTokens()
    {
        foreach ($this->_tokens as $token) {
            if (method_exists($token, 'dump') && ($result = $token->dump($this->_text)) !== '') {
                echo $result.PHP_EOL;
            }
        }
    }
}