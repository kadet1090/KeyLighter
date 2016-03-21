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

    public static function resolve(array $array, $key, $fallback = null)
    {
        do {
            if (isset($array[$key])) {
                return $array[$key];
            }

            $key = StringHelper::pop($key);
        } while (!empty($key));

        return $fallback;
    }
}
