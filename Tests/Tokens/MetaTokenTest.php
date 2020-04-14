<?php

declare(strict_types=1);

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

namespace Kadet\Highlighter\Tests\Tokens;

use Kadet\Highlighter\Parser\Token\MetaToken;
use Kadet\Highlighter\Parser\TokenIterator;

class MetaTokenTest extends TokenTestCase
{
    /**
     * @uses \Kadet\Highlighter\Parser\Context
     * @uses \Kadet\Highlighter\Parser\TokenIterator
     */
    public function testProcessStart()
    {
        $token = new MetaToken('test', ['pos' => 0]);
        $token->setEnd(false);
        $token->setValid(true);

        
        $iterator = new TokenIterator([$token->id => $token], '');

        $this->_result->expects($this->never())->method('append')->withAnyParameters();

        $token->process($this->_context, $this->_language, $this->_result, $iterator);

        $this->assertEquals([$token->id => $token->name], $this->_context->stack);
    }
}
