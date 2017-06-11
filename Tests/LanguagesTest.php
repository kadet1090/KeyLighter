<?php

namespace Kadet\Highlighter\Tests;

use Kadet\Highlighter\KeyLighter;
use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Tests\Helpers\TestFormatter;

class LanguagesTest extends \PHPUnit_Framework_TestCase
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
        $this->assertEquals(
            file_get_contents($expected),
            $this->_keylighter->highlight(file_get_contents($input), $language, $this->_formatter)
        );
    }
}
