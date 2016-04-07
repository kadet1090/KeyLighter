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

use Kadet\Highlighter\Formatter\CliFormatter;
use Kadet\Highlighter\Formatter\HtmlFormatter;
use Kadet\Highlighter;
use Kadet\Highlighter\KeyLighter;
use Kadet\Highlighter\Matcher\SubStringMatcher;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage;

class KeyLighterTest extends \PHPUnit_Framework_TestCase
{
    public function testLanguageRegistering()
    {
        $keylighter = new KeyLighter();
        $keylighter->registerLanguage('Kadet\Highlighter\Tests\Mocks\MockLanguage', ['mock', 'test']);

        $this->assertInstanceOf('Kadet\Highlighter\Tests\Mocks\MockLanguage', $keylighter->getLanguage('mock'));
        $this->assertInstanceOf('Kadet\Highlighter\Tests\Mocks\MockLanguage', $keylighter->getLanguage('test'));

        $this->assertArraySubset([
            'mock' => 'Kadet\Highlighter\Tests\Mocks\MockLanguage',
            'test' => 'Kadet\Highlighter\Tests\Mocks\MockLanguage',
        ], $keylighter->registeredLanguages());
    }

    public function testLanguageEmbedding()
    {
        $keylighter = new KeyLighter();
        $keylighter->registerLanguage('Kadet\Highlighter\Tests\Mocks\MockLanguage', ['mock']);
        $keylighter->registerLanguage('Kadet\Highlighter\Tests\Mocks\MockLanguage', ['test']);

        $this->assertInstanceOf('Kadet\Highlighter\Tests\Mocks\MockLanguage', $keylighter->getLanguage('mock > test'));
        $this->assertContainsOnlyInstancesOf(
            'Kadet\Highlighter\Tests\Mocks\MockLanguage',
            $keylighter->getLanguage('mock > test')->getEmbedded()
        );
    }

    public function testHighlighting()
    {
        $keylighter = new KeyLighter();

        $formatter = new HtmlFormatter();
        $language  = new MockGreedyLanguage(['rules' => [
            'keyword' => new Rule(new SubStringMatcher('if'))
        ]]);

        $this->assertEquals(
            $formatter->format($language->parse('if test')),
            $keylighter->highlight('if test', $language, $formatter)
        );
    }

    public function testHighlightingWithRegisteredLanguage()
    {
        $keylighter = new KeyLighter();
        $keylighter->registerLanguage('Kadet\Highlighter\Tests\Mocks\MockLanguage', ['mock']);

        $formatter = new HtmlFormatter();

        $this->assertEquals(
            $formatter->format($keylighter->getLanguage('mock')->parse('if test')),
            $keylighter->highlight('if test', 'mock', $formatter)
        );
    }

    public function testFunction()
    {
        $formatter = new HtmlFormatter();
        $language  = new MockGreedyLanguage(['rules' => [
            'keyword' => new Rule(new SubStringMatcher('if'))
        ]]);

        $this->assertEquals(
            $formatter->format($language->parse('if test')),
            Highlighter\highlight('if test', $language, $formatter)
        );
    }

    public function testSingleton()
    {
        $formatter = new HtmlFormatter();
        $language  = new MockGreedyLanguage(['rules' => [
            'keyword' => new Rule(new SubStringMatcher('if'))
        ]]);

        $this->assertEquals(
            $formatter->format($language->parse('if test')),
            KeyLighter::get()->highlight('if test', $language, $formatter)
        );
    }

    public function testDefaultFormatter()
    {
        $this->assertInstanceOf(
            php_sapi_name() === 'cli' ? CliFormatter::class : HtmlFormatter::class,
            KeyLighter::get()->getDefaultFormatter()
        );
    }
}
