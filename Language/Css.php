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
use Kadet\Highlighter\Matcher\SubStringMatcher;
use Kadet\Highlighter\Matcher\WordMatcher;
use Kadet\Highlighter\Parser\CloseRule;
use Kadet\Highlighter\Parser\OpenRule;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\MetaToken;
use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\Highlighter\Parser\Validator\Validator;

class Css extends Language
{

    /**
     * Tokenization rules
     */
    public function setupRules()
    {
        $identifier = '-?[_a-zA-Z]+[_a-zA-Z0-9-]*';
        $at = [
            'charset', 'import', 'namespace',
            'media', 'supports', 'document', 'page', 'font-face', 'keyframes', 'viewport', 'counter-style',
            'font-feature-values', 'swash', 'ornaments', 'annotation', 'stylistic', 'styleset', 'character-variant'
        ];
        
        $this->rules->addMany([
            'string' => CommonFeatures::strings(['single' => '\'', 'double' => '"'], [
                'context' => $this->everywhere()
            ]),

            'meta.declaration' => [
                new OpenRule(new SubStringMatcher('{'), [
                    'context' => ['!meta.declaration.media', '!comment'],
                    'factory' => new TokenFactory(MetaToken::class)
                ]),
                new CloseRule(new SubStringMatcher('}')),
            ],

            'meta.declaration.media' => [
                new Rule(new RegexMatcher('/@media(.*?\{)/'), [
                    'context' => Validator::everywhere(),
                    'factory' => new TokenFactory(MetaToken::class)
                ]),
            ],

            'meta.declaration.rule' => [
                new OpenRule(new RegexMatcher('/@media.*(\()/'), [
                    'context' => ['meta.declaration.media'],
                    'factory' => new TokenFactory(MetaToken::class)
                ]),
                new CloseRule(new SubStringMatcher(')')),
            ],

            'keyword.at-rule' => new Rule(new RegexMatcher('/(@(?:-[a-z]+-)?(?:'.implode('|', $at).'))/'), [
                'priority' => 2
            ]),

            'symbol.selector.id'    => new Rule(new RegexMatcher("/(#$identifier)/i")),
            'symbol.selector.tag'   => new Rule(new RegexMatcher('/(?>[\s}]|^)(?=(\w+)[^;]*\{)/ms')),
            'symbol.selector.class' => new Rule(new RegexMatcher("/(\\.$identifier)/i")),

            'symbol.selector.class.pseudo' => new Rule(new RegexMatcher("/(:{1,2}$identifier)/")),

            'number' => new Rule(new RegexMatcher("/([-+]?[0-9]*\\.?[0-9]+([\\w%]+)?)/"), [
                'context'  => ['meta.declaration', '!constant.color', '!comment', '!symbol', '!comment', '!string'],
                'priority' => 0
            ]),
            
            'symbol.property' => new Rule(new RegexMatcher("/($identifier:)/"), [
                'context' => ['meta.declaration', '!symbol', '!comment'],
                'priority' => 0
            ]),

            'call' => new Rule(new RegexMatcher("/($identifier)\\s*\\((?:(?P<string>[a-z].*?)|.*?)\\)/", [
                1 => Token::NAME,
                'string' => 'string.argument'
            ]), [
                'context' => ['meta.declaration', '!comment', '!string', '!keyword']
            ]),

            'constant.color' => [
                new Rule(new RegexMatcher("/(#[0-9a-f]{3,6})/i"), [
                    'priority' => 2,
                    'context'  => ['meta.declaration', '!comment']
                ]),
                new Rule(new WordMatcher([
                    'white', 'silver', 'gray', 'black', 'red', 'maroon', 'yellow', 'olive',
                    'lime', 'green', 'aqua', 'teal', 'blue', 'navy', 'fuchsia', 'purple'
                ]), [
                    'context'  => ['meta.declaration', '!comment']
                ]),
            ],

            'operator' => new Rule(new WordMatcher(['>', '+', '*', '!important'], ['separated' => false]), [
                'context' => $this->everywhere()
            ]),

            'operator.punctuation' => new Rule(new SubStringMatcher(';'), [
                'context' => $this->everywhere()
            ]),

            'comment' => new Rule(new CommentMatcher([], [['/*', '*/']]), ['context' => $this->everywhere()])
        ]);
    }

    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'css';
    }

    protected function everywhere() {
        static $validator;
        if (!$validator) {
            $validator = new Validator(['!string', '!comment']);
        }

        return $validator;
    }
}
