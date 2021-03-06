<?php

declare(strict_types=1);

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

class PlainText extends GreedyLanguage
{

    /**
     * Tokenization rules
     */
    public function setupRules()
    {
    }

    /** {@inheritdoc} */
    public function getIdentifier()
    {
        return 'plaintext';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['plaintext', 'text', 'none'],
            'mime'      => ['text/plain']
        ];
    }
}
