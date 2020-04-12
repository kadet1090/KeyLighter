<?php

declare(strict_types=1);

/**
 * Highlighter
 *1
 * Copyright (C) 2016, Some right reserved.
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
use Kadet\Highlighter\Matcher\SubStringMatcher;
use Kadet\Highlighter\Parser\CloseRule;
use Kadet\Highlighter\Parser\OpenRule;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\Token;

class Xml extends GreedyLanguage
{
    private const IDENTIFIER = '(?P<namespace>[\w\.-]+:)?(?P<name>[\w\.-]+)';

    /**
     * Tokenization rules
     */
    public function setupRules()
    {
        $this->rules->addMany([
            'tag.open'  => [
                new OpenRule(new RegexMatcher('/(<[\w\.-]+)[:\/>:\s]/')),
                new CloseRule(new SubStringMatcher('>'), ['context' => ['!string', '!comment']])
            ],
            'tag.close' => new Rule(new RegexMatcher('/(<\/' . self::IDENTIFIER . '>)/')),

            'symbol.tag' => new Rule(new RegexMatcher('/<\\/?' . self::IDENTIFIER . '/', [
                'name'      => Token::NAME,
                'namespace' => '$.namespace'
            ]), ['context' => ['tag', '!string']]),

            'symbol.attribute' => new Rule(new RegexMatcher('/' . self::IDENTIFIER . '=/', [
                'name'      => Token::NAME,
                'namespace' => '$.namespace'
            ]), ['context' => ['tag', '!string']]),

            'constant.entity' => new Rule(new RegexMatcher('/(&(?:\#\d+|[a-z])+;)/si')),

            'comment' => new Rule(new CommentMatcher(null, [['<!--', '-->']])),
            'string'  => CommonFeatures::strings(['single' => '\'', 'double' => '"'], ['context' => ['tag']]),
        ]);
    }

    /** {@inheritdoc} */
    public function getIdentifier()
    {
        return 'xml';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['xml'],
            'mime'      => ['application/xml', 'text/xml'],
            'extension' => ['*.xml']
        ];
    }
}
