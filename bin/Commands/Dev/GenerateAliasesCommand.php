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

namespace Kadet\Highlighter\bin\Commands\Dev;


use Kadet\Highlighter\Language\Language;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateAliasesCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if($input->getOption('dry')) {
            $output->writeln($this->generate($output));
        } else {
            file_put_contents(
                __DIR__ . '/../../../Config/aliases.php',
                '<?php return '.$this->generate($output).';'
            );
        }
    }

    protected function configure()
    {
        $this
            ->setName('dev:aliases')
            ->setDescription('Generates Config/aliases.php file')
            ->addOption('dry', 'd', InputOption::VALUE_NONE, 'Dry run (output to stdout instead of file)')
        ;
    }

    protected function generate(OutputInterface $output)
    {
        $dir = __DIR__ . '/../../../Language'.DIRECTORY_SEPARATOR;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        $result = [];

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            $class = '\Kadet\Highlighter\Language\\'.str_replace([$dir, '.php'], '', $file->getPathname());

            $output->writeln(sprintf(
                'Found <info>%s</info>, assuming class <language>%s</language>',
                $file->getBasename(), $class
            ), OutputInterface::VERBOSITY_VERBOSE);


            if($metadata = $this->process($output, $class)) {
                $result[] = $metadata;
            }
        }

        return var_export($result, true);
    }

    /**
     * @param OutputInterface $output
     * @param                 $class
     *
     * @return array|false
     */
    protected function process(OutputInterface $output, $class)
    {
        $reflection = new \ReflectionClass($class);
        if ($reflection->isAbstract()) {
            $output->writeln(sprintf(
                '<language>%s</language> is abstract, skipping...',
                $reflection->getName()
            ), OutputInterface::VERBOSITY_VERBOSE);
            return false;
        }

        if (!$reflection->isSubclassOf(Language::class)) {
            $output->writeln(sprintf(
                '<language>%s</language> is not Language, skipping...',
                $reflection->getName()
            ), OutputInterface::VERBOSITY_VERBOSE);
            return false;
        }

        if ($reflection->getMethod('getAliases')->getDeclaringClass()->getName() !== $reflection->getName()) {
            $output->writeln(sprintf(
                '<language>%s</language>::<info>getAliases</info> is not declared, skipping...',
                $reflection->getName()
            ), OutputInterface::VERBOSITY_VERBOSE);
            return false;
        }

        $result = array_merge([$reflection->getName()], call_user_func([$reflection->getName(), 'getAliases']));
        $output->writeln(var_export($result, true), OutputInterface::VERBOSITY_VERBOSE);

        return $result;
    }
}
