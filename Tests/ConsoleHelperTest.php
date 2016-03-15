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


use Kadet\Highlighter\Utils\Console;
use Kadet\Highlighter\Utils\ConsoleHelper;

class ConsoleHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testReset() {
        $this->assertEquals("\033[0m", Console::reset());
    }

    public function testStyled() {
        $this->assertEquals("\e[31mtest\033[0m", Console::styled(["color" =>"red"], "test"));
    }

    public function testStacking() {
        $this->assertEquals(
            "\033[31mtest\033[32mtest2\033[0m\033[31mtest3\033[0m",
            Console::open(["color" => "red"]).
                "test".
                    Console::open(["color" => "green"])."test2".Console::close().
                "test3".
            Console::close()
        );
    }

    /**
     * @dataProvider stylesProvider
     */
    public function testStyles($expected, $style) {
        $console = new ConsoleHelper();
        $this->assertEquals($expected, $console->open($style));
    }

    public function stylesProvider() {
        return [
            'background red' => ["\033[41m", ["background" => "red"]],
            'bold'           => ["\033[1m",  ["bold" => true]],
            'dim'            => ["\033[2m",  ["dim" => true]],
            'underline'      => ["\033[4m",  ["underline" => true]],
            'blink'          => ["\033[5m",  ["blink" => true]],
            'invert'         => ["\033[7m",  ["invert" => true]],

            'wrong'         => [null,  ["wrong" => true]],

            'bg and bold' => ["\033[41;1m",  ["background" => "red", 'bold' => true]],

            'color, bg and bold' => ["\033[31;41;1m",  ["color" => "red", "background" => "red", 'bold' => true]],
        ];
    }
}