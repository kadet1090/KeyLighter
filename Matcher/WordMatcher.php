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

namespace Kadet\Highlighter\Matcher;

class WordMatcher extends RegexMatcher
{

    /**
     * WordMatcher constructor.
     *
     * @param array $words
     * @param array $options
     */
    public function __construct(array $words, array $options = [])
    {
        $options = array_merge([
            'escape'           => true,
            'separated'        => true,
            'case-sensitivity' => false,
        ], $options);

        if ($options['escape']) {
            $words = array_map(function ($word) {
                return preg_quote($word, '/');
            }, $words);
        }

        $regex = '(' . implode('|', $words) . ')';
        if ($options['separated']) {
            $regex = '\b' . $regex . '\b';
        }

        $regex = "/$regex/";
        if (!$options['case-sensitivity']) {
            $regex .= 'i';
        }

        parent::__construct($regex);
    }
}
