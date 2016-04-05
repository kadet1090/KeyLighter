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
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Validator\Validator;

class Markdown extends Language
{
    /**
     * Tokenization rules setup
     */
    public function setupRules()
    {
        $this->rules->validator = new Validator(['!format.block.code', '!format.monospace', '!keyword.escape', '!operator']);
        $this->rules->addMany([
            'format.header' => [
                new Rule(new RegexMatcher('/^(#+.+?)$/m')),
                new Rule(new RegexMatcher('/^(.+?)^(?:-+|=+)$/m'))
            ],
            /*'format' => new Rule(new RegexMatcher('/(?>(?P<bold>[*_]{2}).*?\g{bold}|(?P<italics>[*_]).*?\g{italics})/', [
                'bold' => '$.bold',
                'italics' => '$.italics',
            ]), [
                'factory'  => new TokenFactory(ContextualToken::class),
                'priority' => 0
            ]),*/
            //'format.italics'   => new Rule(new RegexMatcher('/(?:^|[^*_])(([*_])+?\2)/')),
            'format.strike'    => new Rule(new RegexMatcher('/(~~.+?~~)/')),
            'format.monospace' => new Rule(new RegexMatcher('/(`.+?`)/')),

            'operator.list.ordered'   => new Rule(new RegexMatcher('/^\s*(\d+[.)])/m')),
            'operator.list.unordered' => new Rule(new RegexMatcher('/^\s*([-+*])/m')),

            'string.quote' => new Rule(new RegexMatcher('/((?:^>.*?\n)+)/m')),
            'format.block.code'  => new Rule(new RegexMatcher('/^```.*?\n(.*?)^```/ms')),

            'keyword.escape' => new Rule(new RegexMatcher('/(\\\.)/'))
        ]);
    }

    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'markdown';
    }
}
