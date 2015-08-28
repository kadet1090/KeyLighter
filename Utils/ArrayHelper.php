<?php
/**
 * Highlighter
 *1
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


class ArrayHelper
{
    public static function filterByKey(array $array, callable $func)
    {
        return array_intersect_key($array, array_flip(array_filter(array_keys($array), $func)));
    }

    public static function rewindTo(array &$array, callable $test)
    {
        $result = 1;
        while ((list($k, $v) = each($array)) && !($result = $test($k, $v))) {
            ;
        }

        if ($result < 0) {
            prev($array);
        }

        return [key($array), current($array)];
    }

    public static function pushOn(array &$array, $pos, array $elements)
    {
        $first = array_slice($array, 0, $pos);
        $array = array_merge($first, $elements, $array);
    }

    public static function rearrange(array $array, array $keys)
    {
        return array_combine($keys, array_map(function ($key) use ($array) {
            return $array[$key];
        }, $keys));
    }

    public static function column(array $array, $index)
    {
        return array_map(function ($e) use ($index) { return $e[$index]; }, $array);
    }

    public static function find(array $array, callable $tester)
    {
        foreach ($array as $key => $value) {
            if ($tester($key, $value)) {
                return $key;
            }
        }

        return false;
    }
}