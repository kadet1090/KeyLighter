<?php
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
use Kadet\Highlighter\Parser\GreedyParser;
use Kadet\Highlighter\Parser\ParserInterface;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Rules;
use Kadet\Highlighter\Parser\Token\LanguageToken;
use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\Highlighter\Parser\TokenIterator;
use Kadet\Highlighter\Parser\Tokens;
use Kadet\Highlighter\Parser\UnprocessedTokens;
use Kadet\Highlighter\Parser\Validator\Validator;

/**
 * Class Language
 *
 * @package Kadet\Highlighter\Language
 */
abstract class Language
{
    const NOT_EMBEDDED       = false;
    const EMBEDDED           = true;
    const EMBEDDED_BY_PARENT = 2;

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

        $this->rules = new Rules($this);
        $this->setupRules();

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
        $rules = clone $this->rules;
        if(is_bool($embedded)) {
            $rules->addMany(['language.'.$this->getIdentifier() => $this->getEnds($embedded)]);
        }

        foreach ($rules->all() as $rule) {
            yield $rule;
        }

        foreach ($this->getEmbedded() as $language) {
            foreach ($language->_rules(true) as $rule) {
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
     * @param $embedded
     *
     * @return Rule|\Kadet\Highlighter\Parser\Rule[]
     */
    public function getEnds($embedded = false)
    {
        return new Rule(
            new WholeMatcher(), [
                'priority' => 1000,
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
