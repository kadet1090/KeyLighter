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

use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Parser\CloseRule;
use Kadet\Highlighter\Parser\Token\LanguageToken;
use Kadet\Highlighter\Parser\OpenRule;
use Kadet\Highlighter\Parser\TokenFactory;

class Html extends Xml
{
    /**
     * Tokenization rules
     */
    public function setupRules()
    {
        parent::setupRules();

        $css = new Css();
        $js  = new JavaScript();
        $this->rules->addMany([
            'language.'.$js->getIdentifier() => [
                new OpenRule(new RegexMatcher('/<script.*?>()/'), [
                    'factory'     => new TokenFactory(LanguageToken::class),
                    'inject'      => $js,
                    'language'    => $this,
                    'postProcess' => true
                ]),
                new CloseRule(new RegexMatcher('/()<\/script>/'), [
                    'factory'  => new TokenFactory(LanguageToken::class),
                    'language' => $js
                ])
            ],
            'language.'.$css->getIdentifier() => [
                new OpenRule(new RegexMatcher('/<style.*?>()/'), [
                    'factory'     => new TokenFactory(LanguageToken::class),
                    'inject'      => $css,
                    'language'    => $this,
                    'postProcess' => true
                ]),
                new CloseRule(new RegexMatcher('/()<\/style>/'), [
                    'factory'  => new TokenFactory(LanguageToken::class),
                    'language' => $css
                ])
            ]
        ]);
    }

    public function getIdentifier()
    {
        return 'html';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['html'],
            'mime'      => ['text/html'],
            'extension' => ['*.html', '*.htm']
        ];
    }
}
