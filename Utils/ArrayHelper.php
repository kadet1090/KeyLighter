<?php
/**
 * Highlighter
 *1
 * Copyright (C) 2015, Some right reserved.
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
}