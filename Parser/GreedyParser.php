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
use Kadet\Highlighter\Parser\Token\LanguageToken;
use Kadet\Highlighter\Parser\Token\MetaToken;
use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Utils\ArrayHelper;

class GreedyParser implements ParserInterface
{
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
     * @var Language
     */
    private $_language;

    public function __construct(Language $language = null) {
        if($language) {
            $this->setLanguage($language);
        }
    }

    public function setLanguage(Language $language) {
        $this->_language = $language;
    }

    /**
     * @param TokenIterator $tokens
     *
     * @return Result
     */
    public function process(TokenIterator $tokens) {
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

            if ($token->isValid($this->_language, $this->_context)) {
                if(($token->isStart() ? $this->handleStart($token) : $this->handleEnd($token)) === false) {
                    break;
                };
            }
        }

        return $this->_result;
    }

    protected function handleStart(Token $token) {
        if ($token instanceof LanguageToken) {
            $this->_result->merge($token->getInjected()->parse($this->_iterator));
        } else {
            $this->_result->append($token);
            $this->_context[$this->_iterator->key()] = $token->name;
        }

        return true;
    }

    protected function handleEnd(Token $token) {
        $start = $token->getStart();
        
        if ($token instanceof LanguageToken && $token->getLanguage() === $this->_language) {
            $this->_start->setEnd($token);

            if ($this->_start->postProcess) {
                $source = substr($this->_iterator->getSource(), $this->_start->pos, $this->_start->getLength());

                $tokens = $this->_start->getInjected()->tokenize($source, $this->_result, $this->_start->pos, true);
                $this->_result = $this->_start->getInjected()->parse($tokens);
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