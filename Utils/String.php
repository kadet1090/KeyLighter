<?php
namespace Kadet\Highlighter\Utils;

class String
{
    public static function findAll($haystack, $needle)
    {
        $pos = 0;
        $results = [];

        while (true) {
            $result = strpos($haystack, $needle, $pos);
            if ($result === false) {
                break;
            }

            $results[] = $result;
        }

        return $results;
    }

    public static function find($haystack, $needle, $offset = 0)
    {
        if (!is_array($needle)) {
            $needle = [$needle];
        }

        $results = array_map(function ($str) use ($haystack, $offset) {
            return strpos($haystack, $str, $offset);
        }, $needle);
        $results = array_filter($results, function ($a) { return $a !== false; });

        return count($results) !== 0 ? min($results) : false;
    }
}