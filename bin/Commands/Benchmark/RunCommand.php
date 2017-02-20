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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    const DIRECTORY = "../../../Tests/Samples/";

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = __DIR__ . 'BenchmarkCommand.php/' .static::DIRECTORY;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $dir,
                \RecursiveDirectoryIterator::SKIP_DOTS | \RecursiveDirectoryIterator::UNIX_PATHS
            ), \RecursiveIteratorIterator::LEAVES_ONLY
        );

        $formatter = $input->hasOption('formatter')
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
                substr($file->getPathname(), strlen($dir)), $file->getSize(), get_class($language)
            ), OutputInterface::VERBOSITY_VERBOSE);

            // Dry run, to include all necessary files.
            $this->benchmark($source, $language, $formatter);

            $intermediate = [];
            for($i = $input->getOption('times'); $i > 0; $i--) {
                $intermediate = array_merge_recursive($intermediate, $this->benchmark($source, $language, $formatter));

                if($input->getOption('geshi') && class_exists('GeSHi')) {
                    $intermediate['geshi'][] = $this->geshi($source, $file->getExtension());
                }
            }

            $results[$shortname] = [
                'language' => get_class($language),
                'size' => $file->getSize(),
                'times' => $intermediate
            ];
        }

        $this->output([
            'formatter' => get_class($formatter),
            'timestamp' => time(),
            'system'    => php_uname(),
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
            ->addOption('pretty', 'p', InputOption::VALUE_NONE, 'Pretty print')
            ->addOption('geshi', 'g', InputOption::VALUE_NONE, 'Include GeSHi for comparsion')
            ->addOption('formatter', 'f', InputOption::VALUE_OPTIONAL, 'Formatter used for benchmark')
            ->addArgument('output', InputArgument::OPTIONAL, 'Output file')
        ;
    }

    protected function benchmark($source, Language $language, FormatterInterface $formatter, $geshi = null)
    {
        $tokenization = microtime(true);
        $tokens = $language->tokenize($source);
        $tokenization = microtime(true) - $tokenization;

        $parsing = microtime(true);
        $parsed = $language->parse($tokens);
        $parsing = microtime(true) - $parsing;

        $formatting = microtime(true);
        $formatter->format($parsed);
        $formatting = microtime(true) - $formatting;

        $overall = $tokenization + $parsing + $formatting;
        return compact('tokenization', 'parsing', 'formatting', 'overall');
    }

    protected function geshi($source, $language)
    {
        $geshi = microtime(true);
        geshi_highlight($source, $language, null, true);
        return microtime(true) - $geshi;
    }

    protected function output($results, InputInterface $input, OutputInterface $output)
    {
        $result = json_encode($results, $input->getOption('pretty') ? JSON_PRETTY_PRINT : 0);

        if($input->hasArgument('output')) {
            file_put_contents($input->getArgument('output'), $result);
        } else {
            $output->write($result);
        }
    }
}
