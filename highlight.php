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
 *
 * Just Simple CLI App implementation, subject to change.
 */

use Kadet\Highlighter\Utils\Console;

require 'vendor/autoload.php';

$title = Console::open(['color' => 'yellow']).'KeyLighter '.Console::close().'v'.\Kadet\Highlighter\Highlighter::VERSION.' by Kadet';

function printOptions($options) {
    $maxOption = max(array_map(function($option) { return strlen($option[0]); }, $options)) + 4;
    $maxArg    = max(array_map(function($option) { return strlen($option[1]); }, $options)) + 4;

    foreach ($options as $option) {
        list($option, $argument, $description) = $option;

        echo "\t";
        echo str_pad($option, $maxOption, ' ', STR_PAD_RIGHT);
        echo str_pad($argument, $maxArg, ' ', STR_PAD_RIGHT);
        echo $description.PHP_EOL;
    }
}

function getFormatter($name) {
    $formatter = [
        'html' => new \Kadet\Highlighter\Formatter\HtmlFormatter(),
        'cli'  => new \Kadet\Highlighter\Formatter\CliFormatter(),
    ];

    return isset($formatter[$name]) ? $formatter[$name] : null;
}

function getOption($options, $value = true, $default = null) {
    global $argv;

    foreach($options as $option) {
        if($pos = array_search($option, $argv)) {
            $return = !$value || !isset($argv[$pos + 1]) ? $default : $argv[$pos + 1];

            if($value && isset($argv[$pos + 1])) {
                unset($argv[$pos+1]);
            }
            unset($argv[$pos]);

            return $return;
        }
    }

    return false;
}

function benchmark($description, callable $callable) {
    echo Console::open(['color' => 'yellow']).$description.'... '.Console::close();
    $start = microtime(true);
        $return = $callable();
    $done = microtime(true) - $start;
    echo Console::open(['color' => 'green']).'Done! '.Console::close().number_format($done, 3).'s'.PHP_EOL;

    return $return;
}

if($argc == 1) {
    echo $title, PHP_EOL, PHP_EOL;

    echo Console::open(['color' => 'green']).'Usage: '.Console::close(), 'php '.$argv[0].' [options] file', PHP_EOL;

    echo Console::open(['color' => 'green']).'Options: '.Console::close(), PHP_EOL;
    printOptions([
        ['-l, --language', 'language', 'Source Language to highlight, default: xml > php'],
        ['-f, --format',   'format',   'Formatter used to highlight source, for example: html, default: cli'],
        ['-h, --help',      null,      'This screen'],
        ['-v, --verbose',  'level',    'Verbose mode']
    ]);
    exit(0);
}

unset($argv[0]);

$language = \Kadet\Highlighter\Highlighter::getLanguage(getOption(['-l', '--language']) ?: 'php');
$verbose  = getOption(['-v', '--verbose'], true, 1)  ?: 0;
$formatter = getFormatter(getOption(['-f', '--format'])) ?: \Kadet\Highlighter\Highlighter::getDefaultFormatter();
$file = reset($argv);

if($verbose > 0) {
    echo $title.PHP_EOL;
    echo PHP_EOL;
    echo Console::open(['color' => 'green']).'Language:  '.Console::close().get_class($language).PHP_EOL;

    $embedded = $language->getEmbedded();
    if(!empty($embedded)) {
        echo Console::open(['color' => 'green']).'    With:  '.Console::close().implode(', ', array_map('get_class', $embedded)).PHP_EOL;

    }

    echo Console::open(['color' => 'green']).'Formatter: '.Console::close().get_class($formatter).PHP_EOL;
    echo Console::open(['color' => 'green']).'File:      '.Console::close().$file.PHP_EOL;
    echo PHP_EOL;
}

if(!file_exists($file)) {
    echo Console::open(['color' => 'red']).'File not exists.'.Console::close().PHP_EOL;
    die(1);
}

$source = file_get_contents($file);
if($verbose > 1) {
    $tokens    = benchmark('Tokenization', function() use($language, $source) { return $language->tokenize($source); });
    $tokens    = benchmark('Parsing', function() use($language, $tokens) { return $language->parse($tokens); });
    $formatted = benchmark('Formatting', function() use($formatter, $tokens, $source) {
        return $formatter->format($source, new ArrayIterator($tokens));
    });

    echo PHP_EOL, $formatted;
} else {
    echo \Kadet\Highlighter\Highlighter::highlight($source, $language, $formatter);
}

echo PHP_EOL;