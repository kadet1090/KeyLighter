<?php

declare(strict_types=1);

/**
 * Highlighter
 *
 * Copyright (C) 2016, Some right reserved.
 *
 * @author Kacper "Kadet" Donat <kacper@kadet.net>
 *
 * Contact with author:
 * Xmpp: me@kadet.net
 * E-mail: contact@kadet.net
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter\Language;

use Kadet\Highlighter\Matcher\WholeMatcher;
use Kadet\Highlighter\Parser\Context;
use Kadet\Highlighter\Parser\Result;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Rules;
use Kadet\Highlighter\Parser\Token\LanguageToken;
use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\Highlighter\Parser\TokenIterator;
use Kadet\Highlighter\Parser\Tokens;
use Kadet\Highlighter\Parser\UnprocessedTokens;
use Kadet\Highlighter\Parser\Validator\Validator;

/**
 * Greedy Language
 *
 * Implements greedy syntax highlighting.
 *
 * @package Kadet\Highlighter\Language
 */
abstract class GreedyLanguage extends Language
{

    /**
     * @var array
     */
    protected $_options = [];

    /**
     * Tokenizer rules
     *
     * @var Rules
     */
    public $rules;
    
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

        $this->rules = new Rules($this);
        $this->setupRules();
    }

    /**
     * Tokenization rules setup
     */
    abstract public function setupRules();

    /**
     * Parses source and removes wrong tokens.
     *
     * @param TokenIterator|string $tokens
     *
     * @param array                $additional
     * @param bool                 $embedded
     *
     * @return Tokens
     * @throws \InvalidArgumentException
     */
    public function parse($tokens = null, $additional = [], $embedded = false)
    {
        if (is_string($tokens)) {
            $tokens = $this->tokenize($tokens, $additional, $embedded);
        } elseif (!$tokens instanceof TokenIterator) {
            // Todo: Own Exceptions
            throw new \InvalidArgumentException('$tokens must be string or TokenIterator');
        }

        return $this->_process($tokens);
    }

    private function _process(TokenIterator $tokens)
    {
        $context  = new Context($this);
        $result   = new Result($tokens->getSource(), $tokens->current());

        for ($tokens->next(); $tokens->valid(); $tokens->next()) {
            if (!$tokens->current()->process($context, $this, $result, $tokens)) {
                break;
            }
        }

        return $result;
    }

    public function tokenize($source, $additional = [], $offset = 0, $embedded = false)
    {
        return new TokenIterator(
            $this->_tokens($source, $offset, $additional, $embedded)->sort()->toArray(),
            $source,
            $offset
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
            foreach ($rule->match($source) as $token) {
                $result->add($token, $offset);
            }
        }

        return $result->batch($additional);
    }

    /**
     * @param bool $embedded
     *
     * @return \Generator<Rule>
     */
    private function _rules($embedded = false)
    {
        $rules = clone $this->rules;
        if (is_bool($embedded)) {
            $rules->addMany(['language.' . $this->getIdentifier() => $this->getEnds($embedded)]);
        }

        foreach ($rules->all() as $rule) {
            yield $rule;
        }

        // todo: interface
        foreach ($this->getEmbedded() as $language) {
            foreach ($language instanceof GreedyLanguage ? $language->_rules(true) : $language->getEnds(true) as $rule) {
                yield $rule;
            }
        }
    }

    /**
     * Language range Rule(s)
     *
     * @param $embedded
     *
     * @return Rule|Rule[]
     */
    public function getEnds($embedded = false)
    {
        return new Rule(
            new WholeMatcher(),
            [
                'priority' => 10000,
                'factory'  => new TokenFactory(LanguageToken::class),
                'inject'   => $this,
                'language' => null,
                'context'  => Validator::everywhere(),
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
