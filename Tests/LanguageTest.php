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

namespace Kadet\Highlighter\Tests;

require_once __DIR__.'/MatcherTestCase.php';
require_once __DIR__.'/Mocks/MockLanguage.php';

use Kadet\Highlighter\KeyLighter;
use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Matcher\SubStringMatcher;
use Kadet\Highlighter\Parser\CloseRule;
use Kadet\Highlighter\Parser\OpenRule;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\LanguageToken;
use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\Highlighter\Parser\Validator\Validator;
use Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage;

class EmbeddedLanguage extends Mocks\MockGreedyLanguage
{
    public function getEnds($embedded = false)
    {
        return new Rule(new RegexMatcher('/(\{.*?\})/'), [
            'priority' => 1000,
            'factory'  => new TokenFactory(LanguageToken::class),
            'inject'   => $this,
            'language' => null,
            'context'  => Validator::everywhere()
        ]);
    }
}

class LanguageTest extends MatcherTestCase
{

    public function testSimple()
    {
        $language = new Mocks\MockGreedyLanguage(['rules' => [
            'keyword' => new Rule(new SubStringMatcher('if')),
            'number'  => new Rule(new RegexMatcher('/(\d+)/')),
        ]]);

        $this->assertTokens([
            ['start', 'pos' => 0, 'name' => 'language.mock'],
                ['start', 'pos' => 0, 'name' => 'keyword'],
                ['end', 'pos' => 2, 'name' => 'keyword'],
                ['start', 'pos' => 3, 'name' => 'number'],
                ['end', 'pos' => 5, 'name' => 'number'],
            ['end', 'pos' => 5, 'name' => 'language.mock'],
        ], iterator_to_array($language->parse('if 12')), true);
    }

    public function testManyRules()
    {
        $language = new Mocks\MockGreedyLanguage(['rules' => [
            'keyword' => [
                new Rule(new SubStringMatcher('if')),
                new Rule(new SubStringMatcher('or')),
            ]
        ]]);

        $this->assertTokens([
            ['start', 'pos' => 0, 'name' => 'language.mock'],
                ['start', 'pos' => 0, 'name' => 'keyword'],
                ['end', 'pos' => 2, 'name' => 'keyword'],
                ['start', 'pos' => 3, 'name' => 'keyword'],
                ['end', 'pos' => 5, 'name' => 'keyword'],
            ['end', 'pos' => 5, 'name' => 'language.mock'],
        ], iterator_to_array($language->parse('if or')), true);
    }

    public function testNestedTokens()
    {
        $language = new Mocks\MockGreedyLanguage(['rules' => [
            'for' => new Rule(new SubStringMatcher('for')),
            'or'  => new Rule(new SubStringMatcher('or'), ['context' => ['for']]),
        ]]);

        $this->assertTokens([
            ['start', 'pos' => 0, 'name' => 'language.mock'],
                ['start', 'pos' => 0, 'name' => 'for'],
                    ['start', 'pos' => 1, 'name' => 'or'],
                    ['end', 'pos' => 3, 'name' => 'or'],
                ['end', 'pos' => 3, 'name' => 'for'],
            ['end', 'pos' => 3, 'name' => 'language.mock'],
        ], iterator_to_array($language->tokenize('for')), true);
    }

    public function testInvalidTokens()
    {
        $language = new Mocks\MockGreedyLanguage(['rules' => [
            'for' => new Rule(new SubStringMatcher('for')),
            'or'  => new Rule(new SubStringMatcher('or')),
        ]]);

        $this->assertTokens([
            ['start', 'pos' => 0, 'name' => 'language.mock'],
                ['start', 'pos' => 0, 'name' => 'for'],
                ['end', 'pos' => 3, 'name' => 'for'],
            ['end', 'pos' => 3, 'name' => 'language.mock'],
        ], iterator_to_array($language->parse('for')), true);
    }

    public function testLanguageEmbeddingByItself()
    {
        $language = new Mocks\MockGreedyLanguage(['rules' => [
            'keyword' => new Rule(new SubStringMatcher('keyword')),
        ]]);

        $language->embed(new EmbeddedLanguage(['name' => 'embedded']));

        $this->assertTokens([
            ['start', 'pos' => 0, 'name' => 'language.mock'],
                ['start', 'pos' => 0, 'name' => 'keyword'],
                ['end', 'pos' => 7, 'name' => 'keyword'],
                ['start', 'pos' => 8, 'name' => 'language.embedded'],
                ['end', 'pos' => 19, 'name' => 'language.embedded'],
            ['end', 'pos' => 19, 'name' => 'language.mock'],
        ], iterator_to_array($language->parse('keyword { keyword }')), true);
    }

    public function testLanguageEmbeddingByParent()
    {
        $language = new Mocks\MockGreedyLanguage(['rules' => [
            'keyword'           => new Rule(new SubStringMatcher('keyword')),
            'language.embedded' => new Rule(new RegexMatcher('/(\{.*?\})/'), [
                'factory'     => new TokenFactory(LanguageToken::class),
                'inject'      => new EmbeddedLanguage(['name' => 'embedded']),
                'postProcess' => true
            ])
        ]]);

        $this->assertTokens([
            ['start', 'pos' => 0, 'name' => 'language.mock'],
                ['start', 'pos' => 0, 'name' => 'keyword'],
                ['end', 'pos' => 7, 'name' => 'keyword'],
                ['start', 'pos' => 8, 'name' => 'language.embedded'],
                ['end', 'pos' => 19, 'name' => 'language.embedded'],
            ['end', 'pos' => 19, 'name' => 'language.mock'],
        ], iterator_to_array($language->parse('keyword { keyword }')), true);
    }

    public function testUnclosedTokens()
    {
        $language = new Mocks\MockGreedyLanguage(['rules' => [
            'keyword' => new OpenRule(new SubStringMatcher('(')),
        ]]);

        $this->assertTokens([
            ['start', 'pos' => 0, 'name' => 'language.mock'],
                ['start', 'pos' => 3, 'name' => 'keyword'],
                ['end', 'pos' => 7, 'name' => 'keyword'],
            ['end', 'pos' => 7, 'name' => 'language.mock'],
        ], iterator_to_array($language->parse('te ( st')), true);
    }

    public function testRangeTokens()
    {
        $language = new Mocks\MockGreedyLanguage(['rules' => [
            'keyword' => [
                new OpenRule(new SubStringMatcher('(')),
                new CloseRule(new SubStringMatcher(')'))
            ],
        ]]);

        $this->assertTokens([
            ['start', 'pos' => 0, 'name' => 'language.mock'],
                ['start', 'pos' => 4, 'name' => 'keyword'],
                ['end', 'pos' => 11, 'name' => 'keyword'],
            ['end', 'pos' => 15, 'name' => 'language.mock'],
        ], iterator_to_array($language->parse('foo ( bar ) foo')), true);
    }

    public function testOptions()
    {
        $language = new MockGreedyLanguage([
            'test' => 'foo'
        ]);

        $language->bar = 'foo';

        $this->assertEquals('foo', $language->test);
        $this->assertEquals('foo', $language->bar);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWrongArgumentForParse()
    {
        $language = new MockGreedyLanguage([
            'test' => 'foo'
        ]);

        $language->parse(1.23);
    }

    public function testByName()
    {
        KeyLighter::get()->register(MockGreedyLanguage::class, ['name' => ['mock']]);
        $this->assertInstanceOf(MockGreedyLanguage::class, Language::byName('mock'));
    }

    public function testByMime()
    {
        KeyLighter::get()->register(MockGreedyLanguage::class, ['mime' => ['text/x-mock']]);
        $this->assertInstanceOf(MockGreedyLanguage::class, Language::byMime('text/x-mock'));
    }

    public function testByFilename()
    {
        KeyLighter::get()->register(MockGreedyLanguage::class, ['extension' => ['*.mock']]);
        $this->assertInstanceOf(MockGreedyLanguage::class, Language::byFilename('file.mock'));
    }

    public function testReturnFQN()
    {
        $language = new MockGreedyLanguage([]);

        $this->assertSame(MockGreedyLanguage::class, $language->getFQN());
    }

    public function testReturnFQNWithEmbedded()
    {
        $language = new MockGreedyLanguage(['embedded' => [new MockGreedyLanguage([])]]);

        $this->assertSame(MockGreedyLanguage::class.' + '.MockGreedyLanguage::class, $language->getFQN());
    }
}
