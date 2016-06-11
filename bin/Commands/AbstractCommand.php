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
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class AbstractCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->getFormatter()->setStyle('command'  , new OutputFormatterStyle('yellow'));
        $output->getFormatter()->setStyle('language' , new OutputFormatterStyle('blue'));
        $output->getFormatter()->setStyle('formatter', new OutputFormatterStyle('cyan'));
        $output->getFormatter()->setStyle('path'     , new OutputFormatterStyle('green'));
        $output->getFormatter()->setStyle('success'  , new OutputFormatterStyle('green'));
    }
}
