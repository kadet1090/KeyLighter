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


use Kadet\Highlighter\bin\VerboseOutput;
use Kadet\Highlighter\KeyLighter;
use Kadet\Highlighter\Language\Language;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

class HighlightCommand extends Command
{
    protected $_debug = ['time', 'detailed-time', 'count', 'tree-before', 'tree-after', 'density'];

    protected function configure()
    {
        $this->setName('highlight')
            ->addArgument('path', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'File(s) to highlight')
            ->addOption('language', 'l', InputOption::VALUE_OPTIONAL, 'Source Language to highlight, see <comment>list-languages</comment> command')
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'Output format, see <comment>list-languages</comment> command', 'cli')
            ->addOption(
                'debug', 'd', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Debug features (only in verbose): '.implode(', ', array_map(function($f) { return "<info>{$f}</info>"; }, $this->_debug))
            )
            ->setDescription('<comment>[DEFAULT]</comment> Highlights given file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if(!empty($input->getOption('debug')) && $output->getVerbosity() < OutputInterface::VERBOSITY_VERBOSE) {
            $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
        }

        $output->writeln($this->getApplication()->getLongVersion()."\n", Output::VERBOSITY_VERBOSE);
        $formatter = KeyLighter::get()->getFormatter($input->getOption('format')) ?: KeyLighter::get()->getDefaultFormatter();

        foreach($input->getArgument('path') as $filename) {
            $language = $input->getOption('language')
                ? Language::byName($input->getOption('language'))
                : Language::byFilename($filename);

            if(!($source = $this->content($filename))) {
                throw new InvalidArgumentException(sprintf('Specified file %s doesn\'t exist, check if given path is correct.', $filename));
            }

            if($output->isVerbose()) {
                $output->writeln(sprintf(
                    "Used file: <path>%s</path>, Language: <language>%s</language>, Formatter: <formatter>%s</formatter>",
                    $filename, $language->getFQN(), get_class($formatter)
                ));

                $verbose = new VerboseOutput($output, $input, $language, $formatter, $source);
                $formatted = $verbose->process();
            } else {
                $formatted = KeyLighter::get()->highlight($source, $language, $formatter);
            }
            
            if(!$input->getOption('no-output')) {
                $output->writeln($formatted);
            }
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

    public function mergeApplicationDefinition($arguments = true)
    {
        parent::mergeApplicationDefinition($this->getApplication()->explicit);
    }
}
