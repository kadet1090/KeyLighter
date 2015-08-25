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

use Kadet\Highlighter\Matcher\WholeMatcher;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token;
use Kadet\Highlighter\Parser\TokenList\FixableTokenList;
use Kadet\Highlighter\Parser\TokenList\SimpleTokenList;
use Kadet\Highlighter\Parser\TokenList\TokenListInterface;
use Kadet\Highlighter\Utils\ArrayHelper;

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

        $this->_rules['language.' . $this->getIdentifier()] = $this->getOpenClose();

        foreach ($this->_rules as $name => $rules) {
            if (!is_array($rules)) {
                $rules = [$rules];
            }

            /** @var Rule $rule */
            foreach ($rules as $rule) {
                if($name != 'language.' . $this->getIdentifier()) {
                    $rule->setLanguage($this->getIdentifier());
                }

                $this->_tokens->save($rule->match($this->_source), $rule, $name);
            }
        }
    }

    public abstract function getRules();

    public function tokens()
    {
        if ($this->_tokens === null) {
            $this->parse();
        }

        return $this->_tokens;
    }

    public function __dumpTokens()
    {
        $tokens = $this->tokens();

        $deep = 0;
        /** @var Token $token */
        foreach ($tokens as $token) {
            if ($token->isEnd()) {
                $deep--;
            }
            echo str_repeat('  ', $deep) . $token->dump($this->_source) . PHP_EOL;
            if ($token->isStart()) {
                $deep++;
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

    public function parse()
    {

        $this->tokenize();

        if ($this->_tokens instanceof FixableTokenList) {
            $this->_tokens->beforeParse();
        }
        $contexts = [['language.plaintext', [new Token(['pos' => 0, 'name' => 'language.plaintext'])]]];
        /** @var Token $token */
        foreach ($this->_tokens as $token) {
            $context = &$contexts[count($contexts) - 1];

            if ($token->isStart() && $token->getRule()->validateContext($context)) {
                if(fnmatch('language.*', $token->name)) {
                    $contexts[] = [$token->name, [$token]];
                } else {
                    $context[1][spl_object_hash($token)] = $token;
                }
                continue;
            } elseif ($token->isEnd() && $token->isValid()) {
                $start = $token->getStart();

                if ($token->getRule()->validateContext($context, [$token->name])) {
                    if (fnmatch('language.*', $token->name)) {
                        /** @noinspection PhpUnusedParameterInspection */
                        $key = ArrayHelper::find(array_reverse($contexts, true), function ($k, $v) use ($token) {
                            return $v[0] == $token->name;
                        });
                        unset($contexts[$key]);
                        $contexts = array_values($contexts);
                    } else {
                        if ($start != null) {
                            unset($context[1][spl_object_hash($start)]);
                            continue;
                        } else {
                            /** @noinspection PhpUnusedParameterInspection */
                            $start = ArrayHelper::find(array_reverse($context[1]), function ($k, $v) use ($token) {
                                return $v->name == $token->name;
                            });

                            if ($start !== false) {
                                $token->setStart($context[1][$start]);
                                unset($context[1][$start]);

                                continue;
                            }
                        }
                    }
                }
            }

            $token->invalidate();
            $this->_tokens->remove($token);
        }
        if ($this->_tokens instanceof FixableTokenList) {
            $this->_tokens->afterParse();
        }
    }

    public function getOpenClose()
    {
        return new Rule(new WholeMatcher());
    }

    public abstract function getIdentifier();
}