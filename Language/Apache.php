<?php

/**
 * Highlighter
 *1
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
use Kadet\Highlighter\Parser\Token\Token;

class Apache extends GreedyLanguage
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
                new CloseRule(new SubStringMatcher('>'), ['context' => ['!string', '!comment']]),
            ],
            'tag.close' => new Rule(new RegexMatcher('/(<\/' . self::IDENTIFIER . '>)/')),

            'symbol.tag' => new Rule(new RegexMatcher('/<\\/?' . self::IDENTIFIER . '/', [
                'name'      => Token::NAME,
                'namespace' => '$.namespace',
            ]), ['context' => ['tag', '!string']]),

            'number'  => new Rule(new RegexMatcher('/\s(-?(?:0[0-7]+|0[xX][0-9a-fA-F]+|0b[01]+|\d+(?:\.\d+)?))/')),
            'comment' => new Rule(new CommentMatcher(['#'])),
            'string'  => [
                CommonFeatures::strings(['single' => '\'', 'double' => '"'], ['context' => ['!comment', '!operator.escape']]),
                'path' => new Rule(new RegexMatcher('/\B(\/[^\s\)\(]*)/'))
            ],

            'call'    => new Rule(new RegexMatcher('/^\s*(\w+)/mi')),
            'variable'   => new Rule(new RegexMatcher('/%\{([\w-]+)\}/i'), ['context' => ['expression']]),

            'expression' => new Rule(new RegexMatcher('/(%\{.*?\})/i'), ['context' => ['string']]),
            'expression.regex' => [
                new Rule(new RegexMatcher('/(\[(?>[^\[\]]+|(?1))*\])/'), ['context' => ['string']]),
            ],

            'number.ip' => [
                new Rule(new RegexMatcher('/(\d{1,3}(\.\d{1,3}){3})/i'), ['context' => ['!string']])
            ],
            'constant' => new Rule(new WordMatcher(['On', 'Off', 'None', 'All', 'Any'])),

            'operator.escape' => new Rule(new RegexMatcher('/(\\\(?:\R|.|[0-7]{3}|x\x{2}))/s'), [
                'context' => ['!comment'],
                'priority' => 10
            ]),
            'operator.punctuation' => new Rule(new RegexMatcher('/([\(\){},;])/i')),
            'keyword.format' => new Rule(
                new RegexMatcher('/(%[\w%][-+#0]?(?:[0-9]+|\*)?(?:\.(?:[0-9]+|\*))?)/'),
                ['context' => ['string']]
            ),
        ]);
    }

    /** {@inheritdoc} */
    public function getIdentifier()
    {
        return 'apache';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['apache'],
            'mime'      => ['application/xml', 'text/xml'],
            'extension' => ['.htaccess'],
        ];
    }
}
