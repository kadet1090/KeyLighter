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
}
