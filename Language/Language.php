<?php
/**
 * Highlighter
 *
 * Copyright (C) 2015, Some right reserved.
 *
 * @author Kacper "Kadet" Donat <kadet1090@gmail.com>
 *
 * Contact with author:
 * Xmpp: kadet@jid.pl
 * E-mail: kadet1090@gmail.com
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter\Language;

use Kadet\Highlighter\Matcher\WholeMatcher;
use Kadet\Highlighter\Parser\LanguageToken;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token;
use Kadet\Highlighter\Utils\ArrayHelper;

/**
 * Class Language
 *
 * @package Kadet\Highlighter\Language
 */
abstract class Language
{
    /**
     * Tokenization rules
     *
     * @var Rule[]
     */
    private $_rules;

    /**
     * @var Language[]
     */
    private $_subLanguages = [];

    /**
     * Language constructor.
     *
     * @param Language[] $subLanguages
     */
    public function __construct(array $subLanguages = []) {
        $this->_subLanguages = $subLanguages;
        $this->_rules = $this->getRules();
    }


    /**
     * Tokenization rules definition
     *
     * @return array
     */
    public abstract function getRules();

    /**
     * Dump all tokens for debugging.
     *
     * @param string $name Token name wildcard matcher.
     *
     * @return string Dumped tokens
     */
    public function __dumpTokens($name = '*')
    {
        $tokens = $this->tokens();

        $deep = 0;
        $result = '';
        /** @var Token $token */
        foreach ($tokens as $token) {
            if (!fnmatch('*', $token->name) && !fnmatch($name . '.*', $token->name)) {
                continue;
            }

            if ($token->isEnd()) {
                $deep--;
            }
            $result .= str_repeat('  ', $deep) . $token->dump($this->_source) . PHP_EOL;
            if ($token->isStart()) {
                $deep++;
            }
        }

        return $result;
    }

    /**
     * Parses source and removes wrong tokens.
     *
     * @param \Iterator $tokens
     *
     * @return Token[]
     */
    public function parse(\Iterator $tokens = null)
    {
        /*if ($tokens->current() !== $this) {
            return []; // todo: throw exception
        }*/

        $start = $tokens->current();
        $result = [$start];

        $context = [];
        $all = [];


        /** @var Token $token */
        for($tokens->next(); $tokens->valid(); $tokens->next()) {
            $token = $tokens->current();

            if (!$token->isValid([$this, $context])) {
                continue;
            }

            if ($token->isStart()) {
                if ($token instanceof LanguageToken) {
                    $result = array_merge($result, $token->getLanguage()->parse($tokens));
                } else {
                    $all[spl_object_hash($token)] = $result[] = $token;
                    $context[spl_object_hash($token)] = $token->name;
                }
            } else {
                $start = $token->getStart();

                if ($token instanceof LanguageToken) {
                    // todo: close unclosed tokens
                    $result[0]->setEnd($token);

                    $result[] = $token;
                    return $result;
                } else {
                    if ($start !== null) {
                        unset($context[spl_object_hash($start)]);
                    } else {
                        /** @noinspection PhpUnusedParameterInspection */
                        $start = ArrayHelper::find(array_reverse($context), function ($k, $v) use ($token) {
                            return $v === $token->name;
                        });

                        if ($start !== false) {
                            $token->setStart($all[$start]);
                            unset($context[$start]);
                        }
                    }

                    $result[] = $token;
                }
            }
        }

        return $result;
    }

    /**
     * Tokenize source
     */
    public function tokenize($source)
    {
        $result = [];
        $this->_rules['language.' . $this->getIdentifier()] = $this->getOpenClose();

        foreach ($this->_rules as $name => $rules) {
            if (!is_array($rules)) {
                $rules = [$rules];
            }

            /** @var Rule $rule */
            foreach ($rules as $rule) {
                $rule->setLanguage($this);
                $tokens = $rule->match($source);

                /** @var Token $token */
                foreach ($tokens as $token) {
                    $token->name = $name . (isset($token->name) ? '.' . $token->name : '');
                    $token->setRule($rule);
                    $result[spl_object_hash($token)] = $token;
                }
            }
        }

        foreach($this->_subLanguages as $language) {
            $result = array_merge($result, $language->tokenize($source));
        }

        return $result;
    }

    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public abstract function getIdentifier();

    /**
     * Language range Rule(s)
     *
     * @return Rule|Rule[]
     */
    public function getOpenClose()
    {
        return new Rule(new WholeMatcher());
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * @param $source
     */
    public function setSource($source)
    {
        $this->_tokens = null;
        $this->_source = $source;
    }
}
