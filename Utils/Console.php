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

namespace Kadet\Highlighter\Utils;

class Console
{
    /**
     * @var ConsoleHelper
     */
    private static $_instance;

    /**
     * @return ConsoleHelper
     */
    public static function get()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new ConsoleHelper();
        }

        return self::$_instance;
    }

    public static function styled($style, $text)
    {
        return self::get()->styled($style, $text);
    }

    public static function open($style)
    {
        return self::get()->open($style);
    }

    public static function close()
    {
        return self::get()->close();
    }

    public static function reset()
    {
        return self::get()->reset();
    }
}
