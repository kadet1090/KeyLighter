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

use Kadet\Highlighter\Utils\StringHelper;
use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{
    public function testConvertingOffsetToLineAndColumn()
    {
        $source = "test".PHP_EOL."test2";

        $this->assertEquals(['line' => 2, 'pos' => 2], StringHelper::positionToLine($source, 4 + strlen(PHP_EOL) + 1));
    }
}
