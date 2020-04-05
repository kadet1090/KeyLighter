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

namespace Kadet\Highlighter\Tests;

use Kadet\Highlighter\Utils\Helper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    public function testComparing()
    {
        $this->assertEquals(-1, Helper::cmp(-1, 0));
        $this->assertEquals(0, Helper::cmp(0, 0));
        $this->assertEquals(1, Helper::cmp(0, -1));
    }
}
