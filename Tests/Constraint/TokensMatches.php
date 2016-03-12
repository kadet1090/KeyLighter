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

namespace Kadet\KeyLighter\Tests\Constraint;


use Kadet\Highlighter\Parser\Token;

class TokensMatches extends \PHPUnit_Framework_Constraint
{
    protected $_tokens;
    protected $_strict;

    public function __construct($tokens, $strict = false) {
        parent::__construct();

        $this->_tokens = array_values($tokens);
        $this->_strict = $strict;
    }


    /**
     * @param Token[] $other
     *
     * @return bool
     */
    protected function matches($other)
    {
        // reset keys
        $other = array_values($other);
        foreach($this->_tokens as $no => $desired) {
            if($this->_strict) {
                if(!$this->testToken($desired, $other[$no])) {
                    return false;
                }
            } else {
                if(!$this->tryToFind($desired, $other)) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function testToken($desired, Token $actual) {
        if(isset($desired['pos']) && $desired['pos'] !== $actual->pos) {
            return false;
        }

        if(isset($desired['name']) && $desired['name'] !== $actual->name) {
            return false;
        }

        if(isset($desired['rule']) && $desired['rule'] !== $actual->getRule()) {
            return false;
        }

        if(isset($desired['index']) && $desired['index'] !== $actual->index) {
            return false;
        }

        if(in_array('start', $desired, true) && !$actual->isStart()) {
            return false;
        }

        if(in_array('end', $desired, true) && !$actual->isEnd()) {
            return false;
        }

        return true;
    }

    protected function tryToFind($desired, &$tokens) {
        foreach($tokens as $no => $token) {
            if($this->testToken($desired, $token)) {
                unset($desired[$no]);
                return true;
            }
        }

        return false;
    }


    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'matches tokens';
    }
}