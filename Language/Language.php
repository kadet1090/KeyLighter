<?php
/**
 * Highlighter
 *
 * Copyright (C) 2015, Some right reserved.
 * @author Kacper "Kadet" Donat <kadet1090@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/legalcode CC BY-SA
 *
 * Contact with author:
 * Xmpp: kadet@jid.pl
 * E-mail: kadet1090@gmail.com
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter\Language;


use Kadet\Highlighter\Parser\EndToken;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\StartToken;
use Kadet\Highlighter\Parser\Token;

abstract class Language
{
    private $_tokens;
    private $_mapping;

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
        $this->_tokens = [];

        foreach ($this->_rules as $name => $rule) {
            $this->_saveTokens($rule->match($this->_text), $name, $rule);
        }

        $this->_fixTokens();
    }

    public abstract function getRules();


    private function _saveTokens($tokens, $prefix, Rule $rule)
    {
        foreach ($tokens as $token) {
            $token->name = $prefix . (isset($token->name) ? '.' . $token->name : '');
            $token->rule = $rule;
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

    private function _fixTokens()
    {
        uasort($this->_tokens, function ($a, $b) {
            return ($a->pos < $b->pos) ? -1 : (int)($a->pos > $b->pos);
        });

        $context = [];

        reset($this->_tokens);
        while(list(, $token) = each($this->_tokens)) {
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
        if($this->_tokens === null) {
            $this->tokenize();
        }

        return $this->_tokens;
    }

    public function __dumpTokens()
    {
        $tokens = $this->tokens();

        foreach ($tokens as $token) {
            if (method_exists($token, 'dump') && ($result = $token->dump($this->_text)) !== '') {
                echo $result.PHP_EOL;
            }
        }
    }
}