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
use Kadet\Highlighter\Matcher\WordMatcher;
use Kadet\Highlighter\Parser\Rule;

class Haskell extends GreedyLanguage
{
    
    protected $_keywords = [
        'as', '(?:data|type)(?:\s+(?:instance|family))?', 'default', 'deriving(?:\s+instance)?', 'forall', 'foreign',
        'hiding', 'infix[lr]?', 'import', 'instance', 'mdo', 'module', 'proc', 'qualified', 'rec', 'where', 'do'
    ];

    /**
     * Tokenization rules
     */
    public function setupRules()
    {
        $this->rules->addMany([
            'keyword' => [
                new Rule(new WordMatcher($this->_keywords, ['escape' => false, 'separated' => true]))
            ],

            'constant' => new Rule(new WordMatcher(['False', 'True', 'Nothing']), ['priority' => 3]),
            'comment'  => new Rule(new CommentMatcher(['--'], [['{-', '-}']]), ['priority' => 20]),

            'symbol.type' => new Rule(new RegexMatcher('/\b(_*[A-Z]\w*)\b/'), ['priority' => 1]),
            'symbol.function' => [
                new Rule(new RegexMatcher('/(_*[a-z]\w*)\s*::/')),
                new Rule(new RegexMatcher('/[;\n](_*[a-z]\w*).*?=/'))
            ],
            'operator.named' => new Rule(new RegexMatcher('/(`\w+`)/')),

            'string'   => CommonFeatures::strings(['single' => '\'', 'double' => '"']),

            'number'          => new Rule(new RegexMatcher('/\b(\d+(?>(?>\.|e[+-]?)\d+)?|0o[0-7]+|0x[0-9a-f])\b/i'), ['context' => ['!string', '!comment']]),
            'operator.escape' => new Rule(new RegexMatcher('/(\\[\\0\'|bnrtZ%_])/'), ['context' => ['string']]),
            'operator'        => new Rule(new RegexMatcher('/([^\r\n\s\w\"\'\`\[\]\(\)\{\}]+)/'), [
                'priority' => -1,
                'context'  => ['!comment', '!string']
            ])
        ]);
    }

    /** {@inheritdoc} */
    public function getIdentifier()
    {
        return 'haskell';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['haskell'],
            'mime'      => ['text/x-haskell'],
            'extension' => ['*.hs']
        ];
    }
}
