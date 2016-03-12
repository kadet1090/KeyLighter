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

namespace Kadet\KeyLighter\Tests;


use Kadet\Highlighter\Utils\StringHelper;

class StringHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertingOffsetToLineAndColumn() {
        $source = "test".PHP_EOL."test2";

        $this->assertEquals(['line' => 2, 'pos' => 2], StringHelper::positionToLine($source, 4 + strlen(PHP_EOL) + 1));
    }
}
