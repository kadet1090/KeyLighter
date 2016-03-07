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
use Kadet\Highlighter\Parser\OpenRule;
use Kadet\Highlighter\Parser\TokenFactory;

class HtmlLanguage extends XmlLanguage
{
    public function getRules()
    {
        $js = new JavaScriptLanguage();

        return array_merge(parent::getRules(), [
            'language.'.$js->getIdentifier() => [
                new OpenRule(new RegexMatcher('/<script.*?>()/'), [
                    'factory'     => new TokenFactory('Kadet\\Highlighter\\Parser\\LanguageToken'),
                    'inject'      => $js,
                    'language'    => $this,
                    'postProcess' => true
                ]),
                new CloseRule(new RegexMatcher('/()<\/script>/'), [
                    'factory'  => new TokenFactory('Kadet\\Highlighter\\Parser\\LanguageToken'),
                    'language' => $js
                ])
            ]
        ]);
    }

    public function getIdentifier()
    {
        return 'html';
    }
}