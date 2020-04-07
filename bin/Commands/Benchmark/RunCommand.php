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

namespace Kadet\Highlighter\bin\Commands\Benchmark;

use Kadet\Highlighter\Formatter\FormatterInterface;
use Kadet\Highlighter\KeyLighter;
use Kadet\Highlighter\Language\Language;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    private const DIRECTORY = "/../../../Tests/Samples/";

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = __DIR__ . static::DIRECTORY;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $dir,
                \RecursiveDirectoryIterator::SKIP_DOTS | \RecursiveDirectoryIterator::UNIX_PATHS
            ),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        $formatter = $input->getOption('formatter')
            ? KeyLighter::get()->getFormatter($input->getOption('formatter'))
            : KeyLighter::get()->getDefaultFormatter();

        $results = [];

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            $shortname = substr($file->getPathname(), strlen($dir));

            if (!fnmatch($input->getOption('include'), $shortname)) {
                $output->writeln(sprintf('Skipping file <info>%s</info>', $shortname), OutputInterface::VERBOSITY_VERBOSE);

                continue;
            }

            $language = Language::byFilename($file->getFilename());
            $source = file_get_contents($file->getPathname());

            $output->writeln(sprintf(
                'File: <info>%s</info> Size: <info>%s</info> Language: <info>%s</info>',
                substr($file->getPathname(), strlen($dir)),
                $file->getSize(),
                get_class($language)
            ), OutputInterface::VERBOSITY_VERBOSE);

            // Dry run, to include all necessary files.
            $this->benchmark($source, $language, $formatter);

            $times  = [];
            $memory = [];

            for ($i = $input->getOption('times'), $progress = new ProgressBar($output, $i); $i > 0; $i--) {
                $progress->display();
                $result = $this->benchmark($source, $language, $formatter);
                $times  = array_merge_recursive($times, $result['times']);
                $memory = array_merge_recursive($memory, $result['memory']);

                if ($input->getOption('geshi') && class_exists('GeSHi')) {
                    $times['geshi'][] = $this->geshi($source, $file->getExtension());
                }

                $progress->advance();
            }
            $output->write(PHP_EOL);

            $results[$shortname] = [
                'language' => get_class($language),
                'size'     => $file->getSize(),
                'times'    => $times,
                'memory'   => $memory
            ];
        }

        $this->output([
            'formatter' => get_class($formatter),
            'timestamp' => time(),
            'system'    => php_uname(),
            'comment'   => $input->getOption('comment'),
            'results'   => $results
        ], $input, $output);
    }

    protected function configure()
    {
        $this
            ->setName('benchmark:run')
            ->setDescription('Tests performance of KeyLighter')
            ->addOption('times', 't', InputOption::VALUE_OPTIONAL, 'How many times each test will be run', 50)
            ->addOption('include', 'i', InputOption::VALUE_OPTIONAL, 'File mask to include', '*')
            ->addOption('comment', 'm', InputOption::VALUE_OPTIONAL, 'Comment to include in file')
            ->addOption('pretty', 'p', InputOption::VALUE_NONE, 'Pretty print')
            ->addOption('geshi', 'g', InputOption::VALUE_NONE, 'Include GeSHi for comparsion')
            ->addOption('formatter', 'f', InputOption::VALUE_OPTIONAL, 'Formatter used for benchmark')
            ->addArgument('output', InputArgument::OPTIONAL, 'Output file')
        ;
    }

    protected function benchmark($source, Language $language, FormatterInterface $formatter, $geshi = null)
    {
        gc_collect_cycles(); // force garbage collector
        $memory = $this->getMemory();

        $tokenization = $this->_benchmark(function () use ($language, $source) {
            return $language->tokenize($source);
        });
        $tokens = $tokenization['result'];

        $parsing = $this->_benchmark(function () use ($language, $tokens) {
            return $language->parse($tokens);
        });
        $parsed = $tokenization['result'];

        $formatting = $this->_benchmark(function () use ($formatter, $parsed) {
            return $formatter->format($parsed);
        });

        return [
            'times' => [
                'tokenization' => $tokenization['time'],
                'parsing'      => $parsing['time'],
                'formatting'   => $formatting['time'],
                'overall'      => $tokenization['time'] + $parsing['time'] + $formatting['time']
            ],
            'memory' => [
                'start' => $memory,
                'tokenization' => $tokenization['memory'],
                'parsing'      => $parsing['memory'],
                'formatting'   => $formatting['memory'],
                'overall' => $this->getMemory() - $memory,
                'end'   => $this->getMemory()
            ]
        ];
    }

    private function _benchmark(\Closure $function)
    {
        $memory = $this->getMemory();
        $time   = $this->getTime();

        $result = $function();

        return [
            'result' => $result,
            'time'   => $this->getTime() - $time,
            'memory' => $this->getMemory() - $memory,
        ];
    }

    protected function geshi($source, $language)
    {
        // Silence GeSHi notices
        $previousErrorReporting = error_reporting(E_ALL ^ E_NOTICE);

        $geshi = microtime(true);
        geshi_highlight($source, $language, null, true);
        $time = microtime(true) - $geshi;

        // Restore previous error reporting level
        error_reporting($previousErrorReporting);

        return $time;
    }

    protected function output($results, InputInterface $input, OutputInterface $output)
    {
        /**
         * @noinspection PhpComposerExtensionStubsInspection
         * Adding ext-json as dev-only dependency still doesn't
         * prevent this issue so we have to ignore this inspection
         */
        $result = json_encode($results, $input->getOption('pretty') ? JSON_PRETTY_PRINT : 0);

        if ($input->getArgument('output')) {
            file_put_contents($input->getArgument('output'), $result);
        } else {
            $output->write($result);
        }
    }

    protected function getMemory()
    {
        return memory_get_usage();
    }

    protected function getTime()
    {
        return microtime(true);
    }
}
