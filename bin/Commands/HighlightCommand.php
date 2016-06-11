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


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HighlightCommand extends Command
{
    protected function configure()
    {
        $this->setName('highlight')
            ->addArgument('path', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'File to highlight')
            ->addOption('language', 'l', InputOption::VALUE_OPTIONAL, 'Source Language to highlight, see <comment>list-languages</comment> command')
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'Output format, see <comment>list-languages</comment> command')
            ->addOption(
                'debug', 'd', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Debug features: <info>time</info>, <info>count</info>, <info>before-tree</info>, <info>after-tree</info>, <info>memory</info>'
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
