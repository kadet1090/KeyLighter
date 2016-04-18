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

use Kadet\Highlighter\KeyLighter;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\TokenIterator;
use Kadet\Highlighter\Parser\Tokens;


/**
 * Class Language
 *
 * @package Kadet\Highlighter\Language
 */
abstract class Language
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
    public abstract function parse($tokens = null, $additional = [], $embedded = false);

    public abstract function tokenize($source, $additional = [], $offset = 0, $embedded = false);

    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public abstract function getIdentifier();

    /**
     * Language range Rule(s)
     *
     * @param $embedded
     *
     * @return Rule|\Kadet\Highlighter\Parser\Rule[]
     */
    public abstract function getEnds($embedded = false);

    /**
     * @return Language[]
     */
    public abstract function getEmbedded();

    /**
     * @param Language $lang
     */
    public abstract function embed(Language $lang);

    public static function byName($name, $params)
    {
        return KeyLighter::get()->languageByName($name, $params);
    }

    public static function byMime($mime, $params)
    {
        return KeyLighter::get()->languageByMime($mime, $params);
    }

    public static function byFileName($filename, $params)
    {
        return KeyLighter::get()->languageByExt($filename, $params);
    }
}
