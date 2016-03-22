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
use Kadet\Highlighter\Parser\GreedyParser;
use Kadet\Highlighter\Parser\ParserInterface;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\LanguageToken;
use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\Highlighter\Parser\TokenIterator;
use Kadet\Highlighter\Parser\Tokens;
use Kadet\Highlighter\Parser\UnprocessedTokens;

/**
 * Class Language
 *
 * @package Kadet\Highlighter\Language
 */
abstract class Language
{
    /**
     * @var array
     */
    protected $_options = [];

    /**
     * Tokenizer rules
     *
     * @var Rule[]|Rule[][]
     */
    private $_rules;

    /**
     * @var GreedyParser
     */
    private $_parser;

    /**
     * Language constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->_options = array_merge([
            'embedded' => []
        ], $this->_options, $options);

        $this->_rules = $this->getRules();

        $this->_parser = $this->getParser();
        $this->_parser->setLanguage($this);
    }

    /**
     * @return ParserInterface
     */
    public function getParser() {
        return new GreedyParser();
    }

    /**
     * Tokenization rules definition
     *
     * @return Rule[]|Rule[][]
     */
    abstract public function getRules();

    /**
     * Parses source and removes wrong tokens.
     *
     * @param TokenIterator|string $tokens
     *
     * @param array                $additional
     * @param bool                 $embedded
     *
     * @return Tokens
     */
    public function parse($tokens = null, $additional = [], $embedded = false)
    {
        if (is_string($tokens)) {
            $tokens = $this->tokenize($tokens, $additional, $embedded);
        } elseif (!$tokens instanceof TokenIterator) {
            // Todo: Own Exceptions
            throw new \InvalidArgumentException('$tokens must be string or TokenIterator');
        }

        return $this->_parser->process($tokens);
    }

    public function tokenize($source, $additional = [], $offset = 0, $embedded = false)
    {
        return new TokenIterator(
            $this->_tokens($source, $offset, $additional, $embedded)->sort()->toArray(), $source
        );
    }

    /**
     * Tokenize source
     *
     * @param                    $source
     *
     * @param int                $offset
     * @param array|\Traversable $additional
     *
     * @param bool               $embedded
     *
     * @return UnprocessedTokens
     */
    private function _tokens($source, $offset = 0, $additional = [], $embedded = false)
    {
        $result = new UnprocessedTokens();

        /** @var Language $language */
        foreach ($this->_rules($embedded) as $rule) {
            $rule->factory->setOffset($offset);
            foreach ($rule->match($source) as $token) {
                $result->add($token);
            }
        }

        return $result->batch($additional);
    }

    /**
     * @param bool $embedded
     *
     * @return Rule[]
     */
    private function _rules($embedded = false)
    {
        $all = $this->_rules;
        if (!$embedded) {
            $all['language.' . $this->getIdentifier()] = $this->getOpenClose();
        }

        // why this code sucks so much? Because RecursiveIterator performance such a lot more.
        foreach ($all as $name => $rules) {
            if (!is_array($rules)) {
                $rules = [$rules];
            }

            /** @var Rule $rule */
            foreach ($rules as $rule) {
                if ($rule->language === false) {
                    $rule->language = $this;
                }

                $rule->factory->setBase($name);

                yield $rule;
            }
        }

        foreach ($this->getEmbedded() as $language) {
            foreach ($language->_rules() as $rule) {
                yield $rule;
            }
        }
    }

    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    abstract public function getIdentifier();

    /**
     * Language range Rule(s)
     *
     * @return Rule|Rule[]
     */
    public function getOpenClose()
    {
        return new Rule(
            new WholeMatcher(), [
                'priority' => 1000,
                'factory'  => new TokenFactory(LanguageToken::class),
                'inject'   => $this,
                'language' => null,
                'context'  => Rule::everywhere(),
            ]
        );
    }

    /**
     * @return Language[]
     */
    public function getEmbedded()
    {
        return $this->_options['embedded'];
    }

    /**
     * @param Language $lang
     */
    public function embed(Language $lang)
    {
        $this->_options['embedded'][] = $lang;
    }

    public function __get($name)
    {
        return isset($this->_options[$name]) ? $this->_options[$name] : null;
    }

    public function __set($name, $value)
    {
        $this->_options[$name] = $value;
    }
}
