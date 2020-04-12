<?php

declare(strict_types=1);

namespace Kadet\Highlighter\Language;

use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\Token;

class TypeScript extends JavaScript
{
    public function setupRules()
    {
        parent::setupRules();

        $this->rules->addMany([
            'symbol.type' => [
                new Rule(new RegexMatcher('/(?:[)\w]\??:|\bas\b)\s*(\w+)/si'), [
                    'context' => ['!meta.json', '!string', '!comment']
                ]),
                new Rule(new RegexMatcher('/(?:(?=(<\w+(?1)?>))|\G)<(\w+)/six', [ 2 => Token::NAME ]), [
                    'context' => ['!meta.json', '!string', '!comment']
                ]),
            ],
        ]);

        $this->rules
            ->rule('symbol.function')
            ->setMatcher(new RegexMatcher('/function\s+([a-z_]\w+)\s*(?>(<(?>(?2)|[^<>])+>)\s*)?\(/i'));

        /** @var \Kadet\Highlighter\Matcher\WordMatcher $keywords */
        $keywords = $this->rules->rule('keyword')->getMatcher();
        $keywords = $keywords->merge(['as']);

        $this->rules->rule('keyword')->setMatcher($keywords);
    }


    public function getIdentifier()
    {
        return 'typescript';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['ts', 'typescript'],
            'mime'      => ['application/typescript', 'application/x-typescript', 'text/x-typescript', 'text/typescript'],
            'extension' => ['*.ts', '*.tsx'],
        ];
    }
}
