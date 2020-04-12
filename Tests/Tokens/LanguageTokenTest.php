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

use Kadet\Highlighter\Parser\Token\LanguageToken;

class LanguageTokenTest extends TokenTestCase
{
    public function testCreation()
    {
        $token = new LanguageToken('name', ['inject' => $this->_language]);

        $this->assertSame($this->_language, $token->inject);
    }
}
