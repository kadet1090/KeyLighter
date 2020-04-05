<?php

namespace Kadet\Highlighter\Tests;

use Kadet\Highlighter\KeyLighter;
use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Tests\Helpers\TestFormatter;
use Kadet\Highlighter\Utils\StringHelper;
use PHPUnit\Framework\TestCase;

class LanguagesTest extends TestCase
{
    /** @var KeyLighter */
    protected $_keylighter;
    /** @var TestFormatter */
    protected $_formatter;

    public function testFileProvider() {
        $dir = realpath(__DIR__.'/Samples');
        $out = realpath(__DIR__.'/Expected/Test');

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $dir,
                \RecursiveDirectoryIterator::SKIP_DOTS | \RecursiveDirectoryIterator::UNIX_PATHS
            ), \RecursiveIteratorIterator::LEAVES_ONLY
        );

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            $pathname = substr($file->getPathname(), strlen($dir) + 1);
            $language = Language::byFilename($pathname);

            yield $pathname => [ $language, $file->getPathname(), "$out/$pathname.tkn" ];
        }
    }

    protected function setUp()
    {
        $this->_formatter  = new TestFormatter();
        $this->_keylighter = new KeyLighter();
    }

    /** @dataProvider testFileProvider */
    public function testIfLanguageGeneratesValidTokens(Language $language, $input, $expected)
    {
        $source = file_get_contents($input);

        if(!file_exists($expected)) {
            $this->markTestSkipped('File with expected tokens was not generated.');
        }
        $expected = file_get_contents($expected);

        $this->assertEquals(
            $expected,
            StringHelper::normalize($this->_keylighter->highlight($source, $language, $this->_formatter)),
            "Not normalized"
        );

        $this->assertEquals(
            StringHelper::normalize($expected),
            $this->_keylighter->highlight(StringHelper::normalize($source), $language, $this->_formatter),
            "Normalized"
        );
    }
}
