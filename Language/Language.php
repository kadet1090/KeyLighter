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
namespace Kadet\Highlighter\Language;

use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\TokenIterator;
use Kadet\Highlighter\Parser\Tokens;


/**
 * Class Language
 *
 * @package Kadet\Highlighter\Language
 */
interface Language
{
    const EMBEDDED_BY_PARENT = 2;
    const EMBEDDED           = true;
    const NOT_EMBEDDED       = false;

    /**
     * Parses source and removes wrong tokens.
     *
     * @param TokenIterator|string $tokens
     *
     * @param array                $additional
     * @param bool                 $embedded
     *
     * @return Tokens
     */
    public function parse($tokens = null, $additional = [], $embedded = false);

    public function tokenize($source, $additional = [], $offset = 0, $embedded = false);

    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Language range Rule(s)
     *
     * @param $embedded
     *
     * @return Rule|\Kadet\Highlighter\Parser\Rule[]
     */
    public function getEnds($embedded = false);

    /**
     * @return Language[]
     */
    public function getEmbedded();

    /**
     * @param Language $lang
     */
    public function embed(Language $lang);
}
