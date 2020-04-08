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

namespace Kadet\Highlighter\Tests\Mocks;

use Kadet\Highlighter\Language\GreedyLanguage;

class MockGreedyLanguage extends GreedyLanguage
{
    private $_rules;
    private $range;
    private $name;

    public function __construct(array $options)
    {
        $options = array_merge([
            'rules' => [],
            'range' => parent::getEnds(true),
            'name'  => 'mock',
        ], $options);

        $this->_rules = $options['rules'];
        $this->range  = $options['range'];
        $this->name   = $options['name'];
        parent::__construct($options);
    }


    /**
     * Tokenization rules definition
     *
     * @return array
     */
    public function setupRules()
    {
        $this->rules->addMany($this->_rules);
    }

    public function getEnds($embedded = false)
    {
        return $this->range;
    }
    
    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->name;
    }
}
