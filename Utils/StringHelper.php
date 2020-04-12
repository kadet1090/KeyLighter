<?php

declare(strict_types=1);

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

class StringHelper
{
    public static function positionToLine($source, $pos)
    {
        $source = substr($source, 0, $pos);
        $last   = strripos($source, "\n"); // \n is both in UNIX and Windows
        if ($last !== false) {
            $last += strlen("\n");
        }

        return [
            'line' => substr_count($source, "\n") + 1,
            'pos'  => $pos - $last + 1,
        ];
    }

    public static function pop($string, $delimiter = '.')
    {
        $array = explode($delimiter, $string);
        array_pop($array);

        return implode($delimiter, $array);
    }

    public static function normalize($string) {
        return str_replace("\r\n", "\n", $string);
    }
}
