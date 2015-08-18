<?php
/**
 * Highlighter
 *
 * Copyright (C) 2015, Some right reserved.
 * @author Kacper "Kadet" Donat <kadet1090@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/legalcode CC BY-SA
 *
 * Contact with author:
 * Xmpp: kadet@jid.pl
 * E-mail: kadet1090@gmail.com
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter\Language;

use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\TokenList\FixableTokenList;
use Kadet\Highlighter\Parser\TokenList\SimpleTokenList;
use Kadet\Highlighter\Parser\TokenList\TokenListInterface;

abstract class Language
{
    /**
     * @var TokenListInterface
     */
    private $_tokens;

    /**
     * @var Rule[]
     */
    private $_rules;

    private $_source;


    /**
     * Parser constructor.
     * @param $source
     */
    public function __construct($source)
    {
        $this->setSource($source);
        $this->_rules = $this->getRules();
    }


    public function tokenize()
    {
        $this->_tokens = new SimpleTokenList();
        foreach ($this->_rules as $name => $rule) {
            $this->_tokens->save($rule->match($this->_source), $name, $rule);
        }

        // $this->__dumpTokens();

        if ($this->_tokens instanceof FixableTokenList) {
            $this->_tokens->fix();
        }
    }

    public abstract function getRules();

    public function tokens()
    {
        if ($this->_tokens === null) {
            $this->tokenize();
        }

        return $this->_tokens;
    }

    public function __dumpTokens()
    {
        $tokens = $this->tokens();

        foreach ($tokens as $token) {
            if (method_exists($token, 'dump') && ($result = $token->dump($this->_source)) !== '') {
                echo $result . PHP_EOL;
            }
        }
    }

    public function setSource($source)
    {
        $this->_tokens = null;
        $this->_source = $source;
    }

    public function getSource()
    {
        return $this->_source;
    }
}