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

class PlainText extends Language
{

    /**
     * Tokenization rules
     */
    public function setupRules() { }

    /** {@inheritdoc} */
    public function getIdentifier()
    {
        return 'plaintext';
    }
}
