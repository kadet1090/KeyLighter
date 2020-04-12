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

use Kadet\Highlighter\Matcher\CommentMatcher;
use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Matcher\WordMatcher;
use Kadet\Highlighter\Parser\Rule;

class Prolog extends GreedyLanguage
{
    /**
     * Tokenization rules
     */
    public function setupRules()
    {
        $this->rules->addMany([
            'keyword' => new Rule(new WordMatcher(['is', 'forall', 'write', 'catch', 'throw', 'garbage_collect'])),

            'constant' => new Rule(new WordMatcher(['fail', 'true', 'No', 'Yes', '_']), ['priority' => 3]),
            'comment'  => new Rule(new CommentMatcher(['%'], [['/*', '*/']]), ['priority' => 20]),

            'symbol.function' => new Rule(new RegexMatcher('/([a-z]\w*)/'), ['priority' => 3]),

            'variable'        => new Rule(new RegexMatcher('/([A-Z]\w*)/')),
            'string'   => CommonFeatures::strings(['single' => '\'', 'double' => '"']),

            'number'          => new Rule(new RegexMatcher('/\b(\d+(?>(?>\.|e[+-]?)\d+)?|0o[0-7]+|0x[0-9a-f])\b/i'), ['context' => ['!string', '!comment']]),
            'operator'        => new Rule(new WordMatcher(['!', '@?>=?', '@?<=?', '\??==?', '[:\\?]?-', '\\\\?\+', '[-*]?->', ':=', '=[\\\\@:]='], ['escape' => false, 'separated' => false]), [
                'priority' => -1,
                'context'  => ['!comment', '!string']
            ]),
            'operator.punctuation' => new Rule(new RegexMatcher('/([,.;])/'))
        ]);
    }

    /** {@inheritdoc} */
    public function getIdentifier()
    {
        return 'prolog';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['prolog'],
            'mime'      => ['text/x-prolog'],
            'extension' => ['*.prolog']
        ];
    }
}
