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

namespace Kadet\Highlighter\bin;


use Kadet\Highlighter\bin\Commands\HighlightCommand;
use Kadet\Highlighter\KeyLighter;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class Application extends SymfonyApplication
{
    protected function getCommandName(InputInterface $input)
    {
        $command = $input->getFirstArgument();
        if(!$command) {
            return 'list';
        }

        try {
            $this->find($command);

            return $command;
        } catch (CommandNotFoundException $e) {
            return 'highlight';
        }
    }

    protected function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), [
            new HighlightCommand()
        ]);
    }

    protected function getDefaultInputDefinition()
    {
        $input = parent::getDefaultInputDefinition();
        $input->setArguments();
        $input->setOptions(array_filter($input->getOptions(), function (InputOption $option) {
            return $option->getShortcut() != 'q';
        }));
        $input->addOption(new InputOption('no-output', 's', InputOption::VALUE_NONE, 'Disables output, useful for debug.'));

        return $input;
    }


    public function __construct()
    {
        parent::__construct('KeyLighter', KeyLighter::VERSION);
        $this->setDefaultCommand('highlight');
    }
}
