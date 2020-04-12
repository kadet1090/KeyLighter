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

namespace Kadet\Highlighter\Language\Css;

use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Parser\Rule;

class Less extends PreProcessor
{
    /**
     * Tokenization rules
     */
    public function setupRules()
    {
        parent::setupRules();
        
        $this->rules->add('variable', new Rule(new RegexMatcher('/(@[\w-]+)/'), [
            'context'  => ['!comment', '!keyword'],
            'priority' => -1
        ]));
    }

    public function getIdentifier()
    {
        return 'less';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['less'],
            'mime'      => ['text/x-less'],
            'extension' => ['*.less']
        ];
    }
}
