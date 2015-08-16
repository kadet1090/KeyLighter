<?php
/**
 * Created by PhpStorm.
 * User: k_don
 * Date: 16.08.2015
 * Time: 19:42
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
            'escape' => true,
            'separated' => true,
            'case-sensitivity' => false
        ], $options);

        if ($options['escape']) {
            $words = array_map('preg_quote', $words);
        }

        $regex = '('.implode('|', $words).')';
        if ($options['separated']) {
            $regex = '\b'.$regex.'\b';
        }

        $regex = "/$regex/";
        if($options['case-sensitivity']) {
            $regex .= 'i';
        }

        parent::__construct($regex);
    }
}