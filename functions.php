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

namespace Kadet\Highlighter;

use Kadet\Highlighter\Formatter\FormatterInterface;
use Kadet\Highlighter\Language\Language;

function highlight($source, Language $language, FormatterInterface $formatter = null)
{
    return KeyLighter::get()->highlight($source, $language, $formatter);
}
