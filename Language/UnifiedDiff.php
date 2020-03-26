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

use Kadet\Highlighter\Matcher\CommentMatcher;
use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\Token;

class UnifiedDiff extends GreedyLanguage
{
    /**
     * Tokenization rules
     */
    public function setupRules()
    {
        $this->rules->addMany([
            'delimiter'      => new Rule(new RegexMatcher("/^(@@ .+? @@)(.*)$/mi", [
                1 => Token::NAME,
                2 => 'comment'
            ])),
            'annotation.diff' => [
                'add' => new Rule(new RegexMatcher('/^(\+\+\+\s(.+?))\R/mi', [
                    1 => Token::NAME,
                    2 => 'symbol.path'
                ])),
                'remove' => new Rule(new RegexMatcher('/^(---\s(.+?))\R/mi', [
                    1 => Token::NAME,
                    2 => 'symbol.path'
                ])),
            ],
            'diff' => [
                'add'    => new Rule(new RegexMatcher('/^(?:^\+.*$)+/mi', [ 0 => Token::NAME ])),
                'remove' => new Rule(new RegexMatcher('/^(?:^-.*$)+/mi', [ 0 => Token::NAME ])),
            ],
        ]);
    }

    /** @inheritdoc */
    public function getIdentifier()
    {
        return 'diff';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['diff'],
            'mime'      => ['text/x-diff', 'text/x-patch', 'application/x-patch', 'application/x-diff'],
            'extension' => ['*.patch', '*.diff']
        ];
    }
}
