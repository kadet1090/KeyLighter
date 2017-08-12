<?php


namespace Kadet\Highlighter\Language;


use Kadet\Highlighter\Language\Python\Django;
use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Matcher\WordMatcher;
use Kadet\Highlighter\Parser\Rule;

class Twig extends Django
{
    public function setupRules()
    {
        parent::setupRules();
        $this->rules->addMany([
            'call' => [
                new Rule(new RegexMatcher('/(\w+)\s*\(/si'), ['priority' => 1]),
                'test' => new Rule(new RegexMatcher('/is(?:\s+not)?\s+(\w+)/si'), ['priority' => 1]),
            ],
            'keyword' => new Rule(new WordMatcher(['only', 'with', 'is', 'not', 'ignore missing', 'in']))
        ]);
    }

    public function getIdentifier()
    {
        return 'twig';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['twig'],
            'mime'      => ['text/x-twig'],
            'extension' => ['*.twig'],
            'standalone' => false,
            'injectable' => true
        ];
    }
}