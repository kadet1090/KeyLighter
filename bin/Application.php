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

declare(strict_types=1);

namespace Kadet\Highlighter\bin;

use Kadet\Highlighter\bin\Commands\Benchmark;
use Kadet\Highlighter\bin\Commands\Dev;
use Kadet\Highlighter\bin\Commands\FormattersCommand;
use Kadet\Highlighter\bin\Commands\HighlightCommand;
use Kadet\Highlighter\bin\Commands\LanguagesCommand;
use Kadet\Highlighter\KeyLighter;
use Kadet\Highlighter\Tests\Helpers\TestFormatter;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends SymfonyApplication
{
    public $explicit = true;

    protected function getCommandName(InputInterface $input): string
    {
        $command = $input->getFirstArgument();
        if (!$command && !$input->hasParameterOption('--help')) {
            return 'list';
        } elseif ($this->has($command)) {
            return $command;
        } else {
            $this->explicit = false;
            return 'highlight';
        }
    }

    protected function getDefaultCommands(): array
    {
        $devcommands = class_exists(TestFormatter::class) ? [
            new Dev\GenerateTableCommand(),
            new Dev\GenerateMetadataCommand(),
            new Benchmark\RunCommand(),
            new Benchmark\AnalyzeCommand(),
            new Commands\Test\RegenerateCommand()
        ] : [];

        return array_merge(parent::getDefaultCommands(), [
            new HighlightCommand(),
            new LanguagesCommand(),
            new FormattersCommand(),
        ], $devcommands);
    }

    protected function getDefaultInputDefinition(): InputDefinition
    {
        $input = parent::getDefaultInputDefinition();
        $input->setOptions(array_filter($input->getOptions(), function (InputOption $option) {
            return $option->getShortcut() != 'q';
        }));
        $input->addOption(new InputOption('no-output', 's', InputOption::VALUE_NONE, 'Disables output, useful for debug'));

        return $input;
    }


    public function __construct()
    {
        parent::__construct('KeyLighter', KeyLighter::VERSION);
        $this->setDefaultCommand('highlight');
    }

    public function run(InputInterface $input = null, OutputInterface $output = null): int
    {
        $output = $output ?: new ConsoleOutput();

        $output->getFormatter()->setStyle('command', $output->getFormatter()->getStyle('info'));
        $output->getFormatter()->setStyle('language', new OutputFormatterStyle('magenta'));
        $output->getFormatter()->setStyle('path', new OutputFormatterStyle('blue'));
        $output->getFormatter()->setStyle('formatter', new OutputFormatterStyle('yellow'));

        return parent::run($input, $output);
    }
}
