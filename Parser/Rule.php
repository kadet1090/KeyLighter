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
 *
 */
class Rule
{
    private $_matcher;
    private $_options;
    
    /**
     * @var Validator
     */
    private $_validator;

    /**
     * @param MatcherInterface|null $matcher
     * @param array                 $options
     */
    public function __construct(MatcherInterface $matcher = null, array $options = [])
    {
        $this->_matcher = $matcher;

        // Default options:
        $options = array_merge([
            'context'  => [],
            'priority' => 1,
            'language' => false,
            'factory'  => new TokenFactory(Token::class),
            'closedBy' => false
        ], $options);

        $this->setContext($options['context']);
        $this->_options = $options;

        $this->factory->setRule($this);
    }

    public function setContext($context) {
        if(is_array($context)) {
            $this->_validator = new Validator($context);
        } elseif(is_callable($context)) {
            $this->_validator = new DelegateValidator($context);
        }elseif($context instanceof Validator) {
            $this->_validator = $context;
        } else {
            throw new \InvalidArgumentException('$context must be valid Validator');
        }
    }

    /**
     * @param $source
     *
     * @return Token[]
     */
    public function match($source)
    {
        return $this->_matcher !== null ? $this->_matcher->match($source, $this->factory) : [];
    }

    public function validate($context, array $additional = [])
    {
        return $this->_validator->validate($context, $additional);
    }

    public function __get($option)
    {
        return isset($this->_options[$option]) ? $this->_options[$option] : null;
    }

    public function __set($option, $value)
    {
        return $this->_options[$option] = $value;
    }
}
