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

require __DIR__.'/../vendor/autoload.php';

$title = Console::styled(['color' => 'yellow'], 'KeyLighter ').'v'.\Kadet\Highlighter\KeyLighter::VERSION.' by Kadet';
$formatters = [
    'html' => new \Kadet\Highlighter\Formatter\HtmlFormatter(),
    'cli'  => new \Kadet\Highlighter\Formatter\CliFormatter(),
];

function printTable($table, $spacing = 4) {
    $max = [];
    foreach(array_keys(reset($table)) as $key) {
        $max[$key] = max(array_map(function($option) use ($key) { return strlen($option[$key]); }, $table)) + $spacing;
    }

    if(isset($key)) {
        $max[$key] = 0; // Reset last
    }

    foreach ($table as $option) {
        echo "\t";
        foreach($option as $key => $cell) {
            echo str_pad($cell, $max[$key], ' ', STR_PAD_RIGHT);
        }
        echo PHP_EOL;
    }
}

function getFormatter($name) {
    global $formatters;

    return isset($formatters[$name]) ? $formatters[$name] : null;
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
    echo Console::styled(['color' => 'yellow'], $description.'... ');
    $start = microtime(true);
        $return = $callable();
    $done = microtime(true) - $start;
    echo Console::styled(['color' => 'green'], 'Done! ').number_format($done, 3).'s'.PHP_EOL;

    return $return;
}

if($argc == 1) {
    echo $title, PHP_EOL, PHP_EOL;

    echo Console::styled(['color' => 'green'], 'Usage: '), 'php '.$argv[0].' [options] file', PHP_EOL;

    echo Console::styled(['color' => 'green'], 'Options: '), PHP_EOL;
    printTable([
        ['-l, --language',    'language', 'Source Language to highlight, default: xml > php'],
        ['-f, --format',      'format',   'Formatter used to highlight source, for example: html, default: cli'],
        ['-h, --help',         null,      'This screen'],
        ['-v, --verbose',     'level',    'Verbose mode'],
        ['-lf, --formatters',  null,      'List available formatters'],
        ['-ll, --languages',   null,      'List available languages'],
        ['-s, --silent',       null,      'No output, '],

    ]);
    exit(0);
}

unset($argv[0]);

if(getOption(['-ll', '--languages'], false, true)) {
    $languages = \Kadet\Highlighter\KeyLighter::registeredLanguages();

    $result = [];
    foreach($languages as $alias => $class) {
        if(!isset($result[$class])) {
            $result[$class] = ['aliases' => [], 'class' => $class];
        }

        $result[$class]['aliases'][] = $alias;
    }

    echo Console::styled(['color' => 'green'], 'Available languages: ').PHP_EOL;

    printTable(array_map(function($language) {
        return [
            implode(', ', $language['aliases']),
            '=>',
            $language['class']
        ];
    }, $result));

    exit(0);
}

if(getOption(['-lf', '--formatters'], false, true)) {
    echo Console::styled(['color' => 'green'], 'Available formatters: ').PHP_EOL;
    printTable(array_map(function($name, $formatter) {
        return [
            Console::styled(['color' => 'yellow'], $name),
            get_class($formatter)
        ];
    }, array_keys($formatters), array_values($formatters)));

    exit(0);
}

$language  = \Kadet\Highlighter\KeyLighter::getLanguage(getOption(['-l', '--language']) ?: 'php');
$verbose   = getOption(['-v', '--verbose'], true, 1)  ?: 0;
$formatter = getFormatter(getOption(['-f', '--format'])) ?: \Kadet\Highlighter\KeyLighter::getDefaultFormatter();
$silent    = getOption(['-s', '--silent'], false, true);

$file = reset($argv);

if($verbose > 0) {
    echo $title.PHP_EOL;
    echo PHP_EOL;
    echo Console::styled(['color' => 'green'], 'Language:  ').get_class($language).PHP_EOL;

    $embedded = $language->getEmbedded();
    if(!empty($embedded)) {
        echo Console::styled(['color' => 'green'], '    With:  ').implode(', ', array_map('get_class', $embedded)).PHP_EOL;

    }

    echo Console::styled(['color' => 'green'], 'Formatter: ').get_class($formatter).PHP_EOL;
    echo Console::styled(['color' => 'green'], 'File:      ').$file.PHP_EOL;
    echo PHP_EOL;
}

if(!file_exists($file)) {
    echo Console::styled(['color' => 'red'], 'File not exists.').PHP_EOL;
    die(1);
}

$source = file_get_contents($file);
if($verbose > 1) {
    $tokens    = benchmark('Tokenization', function() use($language, $source) { return $language->tokenize($source); });
    $tokens    = benchmark('Parsing', function() use($language, $tokens) { return $language->parse($tokens); });
    $formatted = benchmark('Formatting', function() use($formatter, $tokens, $source) {
        return $formatter->format($source, new ArrayIterator($tokens));
    });

    if(function_exists('xdebug_peak_memory_usage')) {
        echo Console::styled(['color' => 'green'], 'Max memory usage: ').number_format(xdebug_peak_memory_usage()/1024/1024, 4).'MBytes'.PHP_EOL;
    }

    echo PHP_EOL;
} else {
    $formatted = \Kadet\Highlighter\KeyLighter::highlight($source, $language, $formatter);
}

if(!$silent) {
    echo $formatted.PHP_EOL;
}