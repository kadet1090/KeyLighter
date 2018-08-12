<?php
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

namespace Kadet\Highlighter\Matcher;

class WordMatcher extends RegexMatcher
{
    private $words   = [];
    private $options = [];

    /**
     * WordMatcher constructor.
     *
     * @param array $words
     * @param array $options
     */
    public function __construct(array $words, array $options = [])
    {
        $this->words   = $words;
        $this->options = $options;

        $options = array_merge([
            'escape'           => true,
            'atomic'           => false,
            'separated'        => true,
            'case-sensitivity' => false,
        ], $options);

        if ($options['escape']) {
            $words = array_map(function ($word) {
                return preg_quote($word, '/');
            }, $words);
        }

        $regex = implode('|', $words);
        if ($options['atomic']) {
            $regex = "(?>$regex)";
        }
        $regex = "($regex)";

        if ($options['separated']) {
            $regex = "\\b$regex\\b";
        }

        $regex = "/$regex/";
        if (!$options['case-sensitivity']) {
            $regex .= 'i';
        }

        parent::__construct($regex);
    }

    public function merge(array $words)
    {
        return new static(array_merge($this->words, $words), $this->options);
    }

    public function subtract(array $words)
    {
        return new static(array_diff($this->words, $words), $this->options);
    }

    public function getWords()
    {
        return $this->words;
    }

    public function getOptions()
    {
        return $this->options;
    }
}
