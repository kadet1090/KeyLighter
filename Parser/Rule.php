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

namespace Kadet\Highlighter\Parser;

use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Matcher\MatcherInterface;
use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\Validator\DelegateValidator;
use Kadet\Highlighter\Parser\Validator\Validator;

/**
 * Class Rule
 *
 * @package Kadet\Highlighter\Parser
 *
 * @property Language              $language
 * @property Language              $inject
 * @property integer               $priority
 * @property string                $type
 * @property TokenFactoryInterface $factory
 * @property string                $name
 *
 */
class Rule
{
    /**
     * @var Validator
     */
    public $validator = false;
    private $_matcher;
    private $_options;
    private $_enabled = true;

    /**
     * @param MatcherInterface|null $matcher
     * @param array                 $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(MatcherInterface $matcher = null, array $options = [])
    {
        $this->_matcher = $matcher;

        // Default options:
        $options = array_merge([
            'priority' => 1,
            'language' => false,
            'factory'  => new TokenFactory(Token::class),
            'enabled'  => true,
            'name'     => null
        ], $options);

        if (isset($options['context'])) {
            $this->setContext($options['context']);
        }

        $this->_options = $options;
        $this->_enabled = $options['enabled'];

        $this->factory->setRule($this);
    }

    public function setContext($context)
    {
        if (is_array($context)) {
            $this->validator = new Validator($context);
        } elseif (is_callable($context)) {
            $this->validator = new DelegateValidator($context);
        } elseif ($context instanceof Validator) {
            $this->validator = $context;
        } else {
            throw new \InvalidArgumentException('$context must be valid Validator');
        }
    }

    public function getMatcher()
    {
        return $this->_matcher;
    }

    public function setMatcher(MatcherInterface $matcher)
    {
        $this->_matcher = $matcher;
    }

    /**
     * @param $source
     *
     * @return Token[]|\Iterator
     */
    public function match($source)
    {
        return $this->_enabled && $this->_matcher !== null ? $this->_matcher->match($source, $this->factory) : [];
    }

    public function __get($option)
    {
        return isset($this->_options[$option]) ? $this->_options[$option] : null;
    }

    public function __set($option, $value)
    {
        return $this->_options[$option] = $value;
    }

    public function enable()
    {
        $this->_enabled = true;
    }

    public function disable()
    {
        $this->_enabled = false;
    }
}
