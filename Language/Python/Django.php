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

namespace Kadet\Highlighter\Language\Python;

use Kadet\Highlighter\Language\CommonFeatures;
use Kadet\Highlighter\Language\GreedyLanguage;
use Kadet\Highlighter\Matcher\CommentMatcher;
use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Matcher\SubStringMatcher;
use Kadet\Highlighter\Parser\CloseRule;
use Kadet\Highlighter\Parser\OpenRule;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\LanguageToken;
use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\Highlighter\Parser\Validator\Validator;

class Django extends GreedyLanguage
{
    /**
     * Tokenization rules setup
     */
    public function setupRules()
    {
        $this->rules->addMany([
            'comment'     => new Rule(new CommentMatcher([], [['{#', '#}']]), ['priority' => 0]),
            'delimiter'   => new Rule(new RegexMatcher('/((\{[{%]|[%}]\}))/')),
            'variable'    => new Rule(new RegexMatcher('/\{\{\s*([a-z]\w*)/')),
            'call'        => new Rule(new RegexMatcher('/\{\{.*?\|([a-z]\w*)/')),
            'call.template-tag' => new Rule(new RegexMatcher('/{%\s*([a-z]\w*)/')),
            'string' => CommonFeatures::strings(['single' => '\'', 'double' => '"'], [
                'context' => ['!string', '!comment']
            ]),
        ]);
    }

    public function getEnds($embedded = false)
    {
        return [
            'expression' => [
                new OpenRule(new SubStringMatcher('{{'), [
                    'factory'  => new TokenFactory(LanguageToken::class),
                    'priority' => 1000,
                    'inject'   => $this,
                    'context'  => Validator::everywhere(),
                    'language' => null
                ]),
                new CloseRule(new SubStringMatcher('}}'), [
                    'factory'  => new TokenFactory(LanguageToken::class),
                    'priority' => 1000,
                    'language' => $this
                ]),
            ],
            'template-tag' => [
                new OpenRule(new SubStringMatcher('{%'), [
                    'factory'  => new TokenFactory(LanguageToken::class),
                    'inject'   => $this,
                    'priority' => 1000,
                    'context'  => Validator::everywhere(),
                    'language' => null
                ]),
                new CloseRule(new SubStringMatcher('%}'), [
                    'factory'  => new TokenFactory(LanguageToken::class),
                    'language' => $this,
                    'priority' => 1000,
                ]),
            ],
            'comment' => [
                new OpenRule(new SubStringMatcher('{#'), [
                    'factory'  => new TokenFactory(LanguageToken::class),
                    'inject'   => $this,
                    'priority' => 1000,
                    'language' => null
                ]),
                new CloseRule(new SubStringMatcher('#}'), [
                    'factory'  => new TokenFactory(LanguageToken::class),
                    'language' => $this,
                    'priority' => 1000,
                ]),
            ]
        ];
    }

    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'python.django';
    }

    public static function getMetadata()
    {
        return [
            'name'       => ['django', 'jinja'],
            'mime'       => ['application/x-django-templating', 'application/x-jinja'],
            'standalone' => false,
            'injectable' => true
        ];
    }
}
