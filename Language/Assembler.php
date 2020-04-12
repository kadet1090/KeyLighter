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
use Kadet\Highlighter\Parser\Validator\Validator;

class Assembler extends GreedyLanguage
{
    protected $registers = ['[re]?[abcd]x', '[abcd][lh]', 'r\d{1,2}', '[re]?[sd]i', '[re]?[si]p', '[re]?flags'];

    /**
     * Tokenization rules setup
     */
    public function setupRules()
    {
        $this->rules->addMany([
            'call' => new Rule(new RegexMatcher('/^\h*(\w[\w\.]*)/im')),
            'operator.escape' => new Rule(new RegexMatcher('/(\\\(?:\R|.|[0-7]{3}|x\x{2}))/s'), [
                'context' => Validator::everywhere()
            ]),

            'keyword.format' => new Rule(
                new RegexMatcher('/(%[diuoxXfFeEgGaAcspn%][-+#0]?(?:[0-9]+|\*)?(?:\.(?:[0-9]+|\*))?)/'),
                ['context' => ['string']]
            ),
            'string' => CommonFeatures::strings(['single' => '\'', 'double' => '"'], [
                'context' => ['!operator.escape', '!comment', '!string'],
            ]),

            'symbol' => [
                'type'  => new Rule(new WordMatcher(['byte', 'word', 'dword', 'qword']), ['name' => 'builtin']),
                'label' => new Rule(new RegexMatcher('/([a-z]\w+:)/i'), ['priority' => 3])
            ],

            'comment' => [
                new Rule(new CommentMatcher([';'], []), ['priority' => 2])
            ],

            'variable.register' => new Rule(new RegexMatcher('/\b(' . implode('|', $this->registers) . ')\b/i')),
            'number' => new Rule(new RegexMatcher('/\b(-?[0-9][0-9a-fA-F]*[tTdDhHOoqQbByY]?)\b/i')),
            'operator' => [
                'punctuation' => new Rule(new RegexMatcher('/(,)/'), ['priority' => 0]),
                new Rule(new RegexMatcher('/([*+-])/'), ['priority' => 0]),
            ],
        ]);
    }

    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'asm';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['asm', 'assembler'],
            'mime'      => ['text/x-asm'],
            'extension' => ['*.asm']
        ];
    }
}
