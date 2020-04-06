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
use PHPUnit\Framework\TestCase;

class KeyLighterTest extends TestCase
{
    public function testLanguageNames()
    {
        $keylighter = new KeyLighter();
        $keylighter->register('Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage', ['name' => ['mock', 'test']]);

        $this->assertInstanceOf('Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage', $keylighter->languageByName('mock'));
        $this->assertInstanceOf('Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage', $keylighter->languageByName('test'));
    }

    public function testReturnsNames()
    {
        $keylighter = new KeyLighter();
        $keylighter->register('Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage', ['name' => ['mock', 'test']]);

        $this->assertEquals([
            'mock' => 'mock',
            'test' => 'mock',
        ], $keylighter->registeredLanguages());
    }

    public function testLanguageMimes()
    {
        $keylighter = new KeyLighter();
        $keylighter->register('Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage', ['mime' => ['text/x-mock', 'application/x-mock']]);

        $this->assertInstanceOf('Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage', $keylighter->languageByMime('text/x-mock'));
        $this->assertInstanceOf('Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage', $keylighter->languageByMime('application/x-mock'));
    }

    public function testReturnsMimeTypes()
    {
        $keylighter = new KeyLighter();
        $keylighter->register('Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage', ['mime' => ['text/x-mock', 'application/x-mock']]);

        $this->assertEquals([
            'text/x-mock'        => 'mock',
            'application/x-mock' => 'mock',
        ], $keylighter->registeredLanguages('mime'));
    }

    public function testLanguageFilenames()
    {
        $keylighter = new KeyLighter();
        $keylighter->register('Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage', ['extension' => ['*.mock']]);

        $this->assertInstanceOf('Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage', $keylighter->languageByExt('test.mock'));
        $this->assertNotInstanceOf('Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage', $keylighter->languageByExt('mock'));
    }

    public function testReturnsExtensions()
    {
        $keylighter = new KeyLighter();
        $keylighter->register('Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage', ['extension' => ['*.mock', 'mck*']]);

        $this->assertEquals([
            '*.mock' => 'mock',
            'mck*'   => 'mock',
        ], $keylighter->registeredLanguages('extension'));
    }

    public function testLanguageEmbedding()
    {
        $keylighter = new KeyLighter();
        $keylighter->register('Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage', ['name' => ['mock']]);
        $keylighter->register('Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage', ['name' => ['test']]);

        $this->assertInstanceOf('Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage', $keylighter->getLanguage('mock > test'));
        $this->assertContainsOnlyInstancesOf(
            'Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage',
            $keylighter->getLanguage('mock > test')->getEmbedded()
        );
    }

    public function testDeprecatedRegistering()
    {
        $keylighter = new KeyLighter();
        $keylighter->registerLanguage('Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage', ['mock', 'test']);

        $this->assertInstanceOf('Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage', $keylighter->languageByName('mock'));
        $this->assertInstanceOf('Kadet\Highlighter\Tests\Mocks\MockGreedyLanguage', $keylighter->languageByName('test'));
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
