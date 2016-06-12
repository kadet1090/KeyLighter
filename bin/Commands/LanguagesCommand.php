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


use Kadet\Highlighter\KeyLighter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LanguagesCommand extends Command
{
    protected $types = ['name', 'mime', 'filename'];
    protected $formatters = [];

    protected function configure()
    {
        $this->setName('languages')
            ->addArgument('by', InputArgument::OPTIONAL, 'Alias type, one of '.implode(', ', array_map(function($f) { return "<info>{$f}</info>"; }, $this->types)), 'name')
            ->addOption('no-group', 'g', InputOption::VALUE_NONE, 'Don\'t group languages by type')
            ->addOption('classes', 'c', InputOption::VALUE_NONE, 'Return fully qualified class names instead of identifiers')
            ->setDescription('Lists available languages')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $languages = KeyLighter::get()->registeredLanguages($input->getArgument('by'), $input->getOption('classes'));

        $table = new Table($output);
        $table->setColumnStyle(0, new TableStyle());
        $table->setHeaders([ucfirst($input->getArgument('by')), $input->getOption('classes') ? 'Class name' : 'Language']);
        $table->setRows(array_map(function($language) {
            return [
                implode(', ', array_map(function ($f) { return "<comment>{$f}</comment>"; }, $language['aliases'])),
                $language['class']
            ];
        }, $input->getOption('no-group') ? $this->processNonGrouped($languages) : $this->processGrouped($languages)));

        $table->setStyle('borderless');
        $table->render();
    }

    protected function processGrouped($languages) {
        $result = [];
        foreach($languages as $alias => $class) {
            if(!isset($result[$class])) {
                $result[$class] = ['aliases' => [], 'class' => $class];
            }

            $result[$class]['aliases'][] = $alias;
        }

        return $result;
    }

    protected function processNonGrouped($languages) {
        $result = [];
        foreach($languages as $alias => $class) {
            $result[] = ['aliases' => [$alias], 'class' => $class];
        }

        return $result;
    }
}
