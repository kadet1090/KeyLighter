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
use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\Highlighter\Parser\TokenIterator;
use Kadet\Highlighter\Utils\ArrayHelper;

/**
 * Class Language
 *
 * @package Kadet\Highlighter\Language
 */
abstract class Language
{
    /**
     * Tokenizer rules
     *
     * @var Rule[]
     */
    private $_rules;

    /**
     * @var array
     */
    private $_options = [];

    /**
     * Language constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = []) {
        $this->_options  = array_merge([
            'embedded' => [],
        ], $options);
        $this->_rules    = $this->getRules();
    }

    /**
     * Tokenization rules definition
     *
     * @return array
     */
    public abstract function getRules();

    /**
     * Parses source and removes wrong tokens.
     *
     * @param TokenIterator|string $tokens
     *
     * @return TokenIterator
     */
    public function parse($tokens = null)
    {
        if (is_string($tokens)) {
            $tokens = $this->tokenize($tokens);
        } elseif(!$tokens instanceof TokenIterator) {
            throw new \InvalidArgumentException('$tokens must be string or TokenIterator');
        }

        $start = $tokens->current();

        /** @var Token[] $result */
        $result = [$start];

        $context = [];
        $all = [];

        /** @var Token $token */
        for($tokens->next(); $tokens->valid(); $tokens->next()) {
            $token = $tokens->current();

            if (!$token->isValid($this, $context)) {
                continue;
            }

            if ($token->isStart()) {
                if ($token instanceof LanguageToken) {
                    /** @noinspection PhpUndefinedMethodInspection bug */
                    $result = array_merge(
                        $result,
                        $token->getLanguage()->parse($tokens)->getTokens()
                    );
                } else {
                    $all[spl_object_hash($token)] = $result[] = $token;
                    $context[spl_object_hash($token)] = $token->name;
                }
            } else {
                $start = $token->getStart();

                if ($token instanceof LanguageToken && $token->getRule()->getLanguage() === $this) {
                    $result[0]->setEnd($token);

                    if($result[0]->getRule()->postProcess) {
                        $embed = $this
                            ->parse(substr($tokens->getSource(), $result[0]->pos, $result[0]->getLength()))
                            ->getTokens($result[0]->pos);

                        $result = array_merge($result, $embed);
                    }

                    # closing unclosed tokens
                    foreach(array_reverse($context) as $hash => $name) {
                        $result[$hash]->setEnd(new Token([$name, 'pos' => $token->pos]));
                    }

                    $result[] = $token;
                    break;
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

        return new TokenIterator($result, $tokens->getSource());
    }

    /**
     * Tokenize source
     *
     * @param $source
     *
     * @return array
     */
    private function _tokens($source)
    {
        $result = [];
        $this->_rules['language.' . $this->getIdentifier()] = $this->getOpenClose();

        foreach ($this->_rules as $name => $rules) {
            if (!is_array($rules)) {
                $rules = [$rules];
            }

            /** @var Rule $rule */
            foreach ($rules as $rule) {
                if($rule->getLanguage() === false) {
                    $rule->setLanguage($this);
                }

                $rule->factory->setBase($name);

                $result = array_merge($result, $rule->match($source));
            }
        }

        foreach($this->getEmbedded() as $language) {
            $result = array_merge($result, $language->_tokens($source));
        }

        return $result;
    }

    public function tokenize($source)
    {
        $iterator = new TokenIterator($this->_tokens($source), $source);
        $iterator->sort();
        $iterator->rewind();
        return $iterator;
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
        return new Rule(
            new WholeMatcher(), [
                'priority' => 1000,
                'factory' => new TokenFactory('Kadet\\Highlighter\\Parser\\LanguageToken'),
                'inject' => $this
            ]
        );
    }

    /**
     * @return Language[]
     */
    public function getEmbedded() {
        return $this->_options['embedded'];
    }

    /**
     * @param Language $lang
     */
    public function embed(Language $lang) {
        $this->_options['embedded'][] = $lang;
    }
}
