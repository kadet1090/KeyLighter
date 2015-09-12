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

namespace Kadet\Highlighter;

class Highlighter
{
    private $languages = [];

    public function __construct($language) {
        if (!is_array($language)) {
           $language = [$language];
        }

        $this->languages = $language;
    }

    public function highlight($source) {

    }
}