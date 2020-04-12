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

use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Parser\Context;
use Kadet\Highlighter\Parser\Result;
use PHPUnit\Framework\TestCase;

abstract class TokenTestCase extends TestCase
{
    /** @var  Context */
    protected $_context;
    /** @var \PHPUnit_Framework_MockObject_MockObject|Language */
    protected $_language;
    /** @var \PHPUnit_Framework_MockObject_MockObject|Result */
    protected $_result;

    public function setUp()
    {
        $this->_result   = $this->getMockBuilder(Result::class)->disableOriginalConstructor()->getMock();
        $this->_language = $this->getMockBuilder(Language::class)->disableOriginalConstructor()->getMock();
        $this->_context  = new Context($this->_language);
    }
}
