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

class Application extends SymfonyApplication
{
    protected function getCommandName(InputInterface $input)
    {
        $command = $input->getFirstArgument();
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

    public function __construct()
    {
        parent::__construct('KeyLighter', KeyLighter::VERSION);
    }
}
