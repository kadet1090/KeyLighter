<?php

declare(strict_types=1);

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

use Kadet\Highlighter\KeyLighter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FormattersCommand extends Command
{
    protected function configure()
    {
        $this->setName('formatters')
            ->addOption('headerless', 'l', InputOption::VALUE_NONE, 'Output table without headers, useful for parsing')
            ->setDescription('Lists available formatters')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $formatters = KeyLighter::get()->registeredFormatters();
        $table = new Table($output);

        if (!$input->getOption('headerless')) {
            $table->setHeaders(['Name', 'Formatter']);
        }

        $table->setRows(array_map(function ($alias, $class) {
            return [
                "<comment>{$alias}</comment>",
                get_class($class)
            ];
        }, array_keys($formatters), array_values($formatters)));

        $table->setStyle($input->getOption('headerless') ? 'compact' : 'borderless');
        $table->render();
    }
}
