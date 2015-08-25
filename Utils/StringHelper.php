<?php
/**
 * Highlighter
 *
 * Copyright (C) 2015, Some right reserved.
 * @author Kacper "Kadet" Donat <kadet1090@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/legalcode CC BY-SA
 *
 * Contact with author:
 * Xmpp: kadet@jid.pl
 * E-mail: kadet1090@gmail.com
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter\Utils;

class StringHelper
{
    public static function findAll($haystack, $needle)
    {
        $pos = 0;
        $results = [];

        while (($pos = strpos($haystack, $needle, $pos)) !== false) {
            $results[$pos] = $needle;
            $pos += strlen($needle);
        }

        return $results;
    }

    public static function find($haystack, $needle, $offset = 0, &$match)
    {
        if (!is_array($needle)) {
            $needle = [$needle];
        }

        $results = array_map(function ($str) use ($haystack, $offset) {
            return [$str, strpos($haystack, $str, $offset)];
        }, $needle);

        if (count($results) === 0) {
            return false;
        }

        $results = array_filter(
            array_combine(ArrayHelper::column($results, 0), ArrayHelper::column($results, 1)),
            function ($a) {
                return $a !== false;
            }
        );
        asort($results);
        reset($results);

        $match = key($results);
        return current($results);
    }
}