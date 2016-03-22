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
use Kadet\Highlighter\Parser\MetaToken;
use Kadet\Highlighter\Parser\Result;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token;
use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\Highlighter\Parser\TokenIterator;
use Kadet\Highlighter\Parser\UnprocessedTokens;
use Kadet\Highlighter\Utils\ArrayHelper;

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
     * @var array
     */
    private $_context;

    /**
     * @var Result
     */
    private $_result;

    /**
     * @var TokenIterator
     */
    private $_iterator;

    /**
     * @var LanguageToken
     */
    private $_start;

    /**
     * Language constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->_options = array_merge([
            'embedded' => [],
        ], $this->_options, $options);

        $this->_rules = $this->getRules();
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
     * @return TokenIterator
     */
    public function parse($tokens = null, $additional = [], $embedded = false)
    {
        if (is_string($tokens)) {
            $tokens = $this->tokenize($tokens, $additional, $embedded);
        } elseif (!$tokens instanceof TokenIterator) {
            // Todo: Own Exceptions
            throw new \InvalidArgumentException('$tokens must be string or TokenIterator');
        }

        // Reset variables to default state
        $this->_start    = $tokens->current();
        $this->_context  = [];
        $this->_result   = new Result($tokens->getSource(), [
            $this->_start
        ]);
        $this->_iterator = $tokens;

        /** @var Token $token */
        for ($tokens->next(); $tokens->valid(); $tokens->next()) {
            $token = $tokens->current();

            if ($token->isValid($this, $this->_context)) {
                if(($token->isStart() ? $this->handleStart($token) : $this->handleEnd($token)) === false) {
                    break;
                };
            }
        }

        return $this->_result;
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
     * @param       $source
     *
     * @param int   $offset
     * @param array $additional
     *
     * @param bool  $embedded
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

    protected function handleStart(Token $token) {
        if ($token instanceof LanguageToken) {
            $this->_result->merge($token->getInjected()->parse($this->_iterator));
        } else {
            $this->_result[] = $token;
            $this->_context[$this->_iterator->key()] = $token->name;
        }

        return true;
    }

    protected function handleEnd(Token $token) {
        $start = $token->getStart();

        /** @noinspection PhpUndefinedMethodInspection bug */
        if ($token instanceof LanguageToken && $token->getLanguage() === $this) {
            $this->_start->setEnd($token);

            if ($this->_start->postProcess) {
                $source = substr($this->_iterator->getSource(), $this->_start->pos, $this->_start->getLength());

                $tokens = $this->tokenize($source, $this->_result, $this->_start->pos, true);
                $this->_result = $this->parse($tokens);
            }

            # closing unclosed tokens
            foreach (array_reverse($this->_context) as $hash => $name) {
                $end = new Token([$name, 'pos' => $token->pos]);
                $this->_iterator[$hash]->setEnd($end);
                $this->_result[] = $end;
            }

            $this->_result[] = $token;
            return false;
        } else {
            if ($start) {
                unset($this->_context[spl_object_hash($start)]);
            } else {
                $start = ArrayHelper::find(array_reverse($this->_context), function ($k, $v) use ($token) {
                    return $v === $token->name;
                });

                if ($start !== false) {
                    $token->setStart($this->_iterator[$start]);
                    unset($this->_context[$start]);
                    $start = $this->_iterator[$start];
                }
            }

            if (!$start instanceof MetaToken) {
                $this->_result[] = $token;
            }
        }

        return true;
    }
}
