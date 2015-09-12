<?php
/**
 * Highlighter
 *
 * Copyright (C) 2015, Some right reserved.
 *
 * @author Kacper "Kadet" Donat <kadet1090@gmail.com>
 *
 * Contact with author:
 * Xmpp: kadet@jid.pl
 * E-mail: kadet1090@gmail.com
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter\Utils;


class Console
{
    private static $_stack   = [];
    private static $_current = [];

    private static $_default = [
        'color'      => 'default',
        'background' => 'default',
        'bold'       => false,
        'underlined' => false,
        'dim'        => false,
        'blink'      => false,
    ];

    public static function open($style)
    {
        if (!empty(self::$_current)) {
            self::$_stack[] = self::$_current;
            $style = array_diff($style, self::$_current);
        }

        self::$_current = array_merge(self::$_current, $style);
        return self::_set(array_diff(self::$_current, self::$_default));
    }

    public static function close()
    {
        self::$_current = empty(self::$_stack) ? self::$_default : array_pop(self::$_stack);
        return "\033[0m".self::_set(array_diff(self::$_current, self::$_default));
    }

    private static function _color($name, $bg = false)
    {
        $colors = [
            'default'       => 39,
            'black'         => 30,
            'red'           => 31,
            'green'         => 32,
            'yellow'        => 33,
            'blue'          => 34,
            'magenta'       => 35,
            'cyan'          => 36,
            'light gray'    => 37,
            'dark gray'     => 90,
            'light red'     => 91,
            'light green'   => 92,
            'light yellow'  => 93,
            'light blue'    => 94,
            'light magenta' => 95,
            'light cyan'    => 96,
            'white'         => 97,
        ];

        return $colors[strtolower($name)] + ($bg ? 10 : 0);
    }

    private static function _style($name, $value) {
        switch($name) {
            case 'color':
                return self::_color($value);
            case 'background':
                return self::_color($value, true);
            case 'bold':
                return $value ? 1 : 21;
            case 'dim':
                return $value ? 2 : 22;
            case 'underline':
                return $value ? 4 : 24;
            case 'blink':
                return $value ? 5 : 25;
            case 'invert':
                return $value ? 7 : 27;
        }

        return null;
    }

    private static function _set($style) {
        $escape = "\033[".implode(';', array_map(function($style, $name) {
                return self::_style($style, $name);
            }, array_keys($style), $style)).'m';

        return $escape;
    }
}