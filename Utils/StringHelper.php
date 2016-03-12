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
        $last = strripos($source, PHP_EOL);
        if ($last !== false) {
            $last += strlen(PHP_EOL);
        }

        return [
            'line' => substr_count($source, PHP_EOL) + 1,
            'pos'  => $pos - $last + 1,
        ];
    }
}