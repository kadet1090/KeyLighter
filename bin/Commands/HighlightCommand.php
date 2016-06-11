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

namespace Kadet\Highlighter\bin\Commands;


use Kadet\Highlighter\Formatter\CliFormatter;
use Kadet\Highlighter\Formatter\DebugFormatter;
use Kadet\Highlighter\Formatter\HtmlFormatter;
use Kadet\Highlighter\KeyLighter;
use Kadet\Highlighter\Language\Language;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HighlightCommand extends AbstractCommand
{
    protected $debug = ['time', 'detailed-time', 'count', 'tree-before', 'tree-after', 'memory'];
    protected $formatters = [];

    protected function configure()
    {
        $this->formatters = [
            'html'  => new HtmlFormatter(),
            'cli'   => new CliFormatter(),
            'debug' => new DebugFormatter(),
        ];

        $this->setName('highlight')
            ->addArgument('path', InputArgument::REQUIRED, 'File to highlight')
            ->addOption('language', 'l', InputOption::VALUE_OPTIONAL, 'Source Language to highlight, see <comment>list-languages</comment> command')
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'Output format, see <comment>list-languages</comment> command', 'cli')
            ->addOption(
                'debug', 'd', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Debug features (only in verbose): '.implode(', ', array_map(function($f) { return "<info>{$f}</info>"; }, $this->debug))
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $filename = $input->getArgument('path');
        $language = $input->getOption('language')
            ? Language::byName($input->getOption('language'))
            : Language::byFilename($filename);

        $formatter = isset($this->formatters[$input->getOption('format')])
            ? $this->formatters[$input->getOption('format')]
            : $this->formatters['cli'];

        if(!($source = $this->content($filename))) {
            throw new InvalidArgumentException(sprintf('Specified file %s doesn\'t exist, check if given path is correct.', $filename));
        }

        if($output->isVerbose()) {
            $counts = ['before' => null, 'after' => null];
            $times  = ['tokenization' => null, 'parsing' => null, 'formatting' => null];

            $output->writeln($this->getApplication()->getLongVersion()."\n");

            $output->writeln(sprintf(
                "Used file: <path>%s</path>, Language: <language>%s</language>, Formatter: <formatter>%s</formatter>",
                $filename, $language->getFQN(), get_class($formatter)
            ));

            $tokens    = $this->benchmark($output, function() use($language, $source) { return $language->tokenize($source); }, $times['tokenization']);
            $counts['before'] = count($tokens);
            if(in_array('tree-before', $input->getOption('debug'))) {
                $output->writeln('<comment>Token tree before parsing: </comment>');
                $output->writeln($this->formatters['debug']->format(clone $tokens, false));
            }

            $tokens    = $this->benchmark($output, function() use($language, $tokens) { return $language->parse($tokens); }, $times['parsing']);
            $counts['after'] = count($tokens);
            if(in_array('tree-after', $input->getOption('debug'))) {
                $output->writeln('<comment>Token tree after parsing: </comment>');
                $output->writeln($this->formatters['debug']->format(clone $tokens));
            }

            $formatted = $this->benchmark($output, function() use($formatter, $tokens) { return $formatter->format($tokens); }, $times['formatting']);

            if(in_array('count', $input->getOption('debug'))) {
                $output->writeln(sprintf(
                    '<comment>Token count before parsing: </comment> %s (%s tokens/kB)',
                    $counts['before'], number_format($counts['before']/strlen($source) * 1024)
                ));
                $output->writeln(sprintf(
                    '<comment>Token count after parsing:  </comment> %s (%s tokens/kB)',
                    $counts['after'], number_format($counts['after']/strlen($source) * 1024)
                ));
            }

            if(in_array('detailed-time', $input->getOption('debug'))) {
                $output->writeln(sprintf(
                    '<info>Time taken</info>  [s]       <comment>tokenization</comment>/<comment>parsing</comment>/<comment>formatting</comment>: %.4f / %.4f / %.4f',
                    $times['tokenization'], $times['parsing'], $times['formatting']
                ));

                $output->writeln(sprintf(
                    '<info>Performance</info> [chars/s] <comment>tokenization</comment>/<comment>parsing</comment>/<comment>formatting</comment>: %s / %s / %s',
                    number_format(strlen($source) / $times['tokenization']),
                    number_format(strlen($source) / $times['parsing']),
                    number_format(strlen($source) / $times['formatting'])
                ));
            }

            if(in_array('time', $input->getOption('debug'))) {
                $output->writeln(sprintf(
                    '<info>Overall:</info> %.4fs, %s chars/s',
                    array_sum($times), number_format(strlen($source) / array_sum($times))
                ));
            }
        } else {
            $formatted = KeyLighter::get()->highlight($source, $language, $formatter);
        }


        if(!$input->getOption('no-output')) {
            $output->write($formatted);
        }
    }

    protected function content($path)
    {
        if(!($file = @fopen($path, 'r'))) {
            return false;
        }

        $content = '';
        while(!feof($file)) {
            $content .= fgets($file);
        }
        fclose($file);

        return $content;
    }

    protected function benchmark(OutputInterface $output, callable $callable, &$time = null) {
        $start = microtime(true);
        $return = $callable();
        $time = microtime(true) - $start;

        return $return;
    }
}
