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


use Kadet\Highlighter\Utils\ArrayHelper;

class ArrayHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testColumn() {
        $array = [
            ['a' => 2, 'b' => 2],
            ['a' => 3, 'c' => 4],
            [0, 'a' => 4]
        ];

        $this->assertEquals([2,3,4], ArrayHelper::column($array, 'a'));
    }

    public function testRearrange() {
        $array = [
            'a' => 0,
            'b' => 2,
            'c' => 4
        ];

        $expected = ['c', 'b', 'a'];
        $this->assertEquals($expected, array_keys(ArrayHelper::rearrange($array, $expected)));
    }

    public function testFind() {
        $array = [
            'a' => 0,
            'b' => 2,
            'c' => 4
        ];

        $this->assertEquals('b', ArrayHelper::find($array, function($k, $v) {
            return $k == 'b';
        }));

        $this->assertFalse(ArrayHelper::find($array, function($k, $v) {
            return $k == 'b' && $v == 3;
        }));
    }
}
