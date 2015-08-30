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
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token;
use Kadet\Highlighter\Parser\TokenList\FixableTokenList;
use Kadet\Highlighter\Parser\TokenList\SimpleTokenList;
use Kadet\Highlighter\Parser\TokenList\TokenListInterface;
use Kadet\Highlighter\Utils\ArrayHelper;

/**
 * Class Language
 *
 * @package Kadet\Highlighter\Language
 */
abstract class Language
{
    /**
     * Token list
     *
     * @var TokenListInterface
     */
    private $_tokens;

    /**
     * Tokenization rules
     *
     * @var Rule[]
     */
    private $_rules;

    /**
     * Source to parse
     *
     * @var string
     */
    private $_source;


    /**
     * Parser constructor.
     *
     * @param $source
     */
    public function __construct($source = '')
    {
        $this->setSource($source);
        $this->_rules = $this->getRules();
    }

    /**
     * Tokenization rules definition
     *
     * @return Rule[]
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
     * Returns highlighting Tokens
     *
     * @return TokenListInterface
     */
    public function tokens()
    {
        if ($this->_tokens === null) {
            $this->parse();
        }

        return $this->_tokens;
    }

    /**
     * Parses source and removes wrong tokens.
     */
    public function parse()
    {
        $this->tokenize();

        if ($this->_tokens instanceof FixableTokenList) {
            $this->_tokens->beforeParse();
        }
        $contexts = [['language.plaintext', ['language.plaintext']]];

        /** @var Token $token */
        foreach ($this->_tokens as $token) {
            $context = &$contexts[count($contexts) - 1];

            if (!$token->isValid($context)) {
                $this->_tokens->remove($token);
                continue;
            }

            if ($token->isStart()) {
                if (fnmatch('language.*', $token->name)) {
                    $contexts[] = [$token->name, [$token->name]];
                } else {
                    $context[1][spl_object_hash($token)] = $token->name;
                }
            } else {
                $start = $token->getStart();

                if (fnmatch('language.*', $token->name)) {
                    /** @noinspection PhpUnusedParameterInspection */
                    $key = ArrayHelper::find(array_reverse($contexts, true), function ($k, $v) use ($token) {
                        return $v[0] === $token->name;
                    });
                    unset($contexts[$key]);
                    $contexts = array_values($contexts);
                } else {
                    if ($start !== null) {
                        unset($context[1][spl_object_hash($start)]);
                    } else {
                        /** @noinspection PhpUnusedParameterInspection */
                        $start = ArrayHelper::find(array_reverse($context[1]), function ($k, $v) use ($token) {
                            return $v === $token->name;
                        });

                        if ($start !== false) {
                            $token->setStart($this->_tokens->get($start));
                            unset($context[1][$start]);
                        }
                    }
                }
            }
        }
        if ($this->_tokens instanceof FixableTokenList) {
            $this->_tokens->afterParse();
        }
    }

    /**
     * Tokenize source
     */
    public function tokenize()
    {
        $this->_tokens = new SimpleTokenList();

        $this->_rules['language.' . $this->getIdentifier()] = $this->getOpenClose();

        foreach ($this->_rules as $name => $rules) {
            if (!is_array($rules)) {
                $rules = [$rules];
            }

            /** @var Rule $rule */
            foreach ($rules as $rule) {
                if ($name !== 'language.' . $this->getIdentifier()) {
                    $rule->setLanguage($this->getIdentifier());
                }

                $this->_tokens->save($rule->match($this->_source), $rule, $name);
            }
        }
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
