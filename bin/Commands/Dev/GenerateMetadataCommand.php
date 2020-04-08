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

namespace Kadet\Highlighter\bin\Commands\Dev;

use Kadet\Highlighter\Language\Language;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarExporter\VarExporter;

class GenerateMetadataCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (! class_exists(VarExporter::class)) {
            $output->writeln(
                '<error>This command will not work if you installed project with composer install --no-dev'
            );
            return 1;
        }

        if ($input->getOption('dry')) {
            $output->writeln($this->generate($output));
        } else {
            file_put_contents(
                __DIR__ . '/../../../Config/metadata.php',
                "<?php\n\nreturn " . $this->generate($output) . ";\n"
            );
        }

        return 0;
    }

    protected function configure()
    {
        $this
            ->setName('dev:metadata')
            ->setDescription('Generates Config/metadata.php file')
            ->addOption('dry', 'd', InputOption::VALUE_NONE, 'Dry run (output to stdout instead of file)')
        ;
    }

    protected function generate(OutputInterface $output)
    {
        $dir = __DIR__ . '/../../../Language' . DIRECTORY_SEPARATOR;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        $result = [];

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            $path = str_replace([$dir, '.php', '/'], ['', '', '\\'], $file->getPathname());
            $class = "\\Kadet\\Highlighter\\Language\\$path";

            $output->writeln(sprintf(
                'Found <info>%s</info>, assuming class <language>%s</language>',
                $file->getBasename(),
                $class
            ), OutputInterface::VERBOSITY_VERBOSE);


            if ($metadata = $this->process($output, $class)) {
                $result[] = $metadata;
            }
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        return VarExporter::export($result);
    }

    /**
     * @param OutputInterface $output
     * @param                 $class
     * @return array|false
     * @throws \ReflectionException
     */
    protected function process(OutputInterface $output, $class)
    {
        $reflection = new ReflectionClass($class);
        if ($reflection->isAbstract()) {
            $output->writeln(sprintf(
                '<language>%s</language> is abstract, skipping...',
                $reflection->name
            ), OutputInterface::VERBOSITY_VERBOSE);
            return false;
        }

        if (!$reflection->isSubclassOf(Language::class)) {
            $output->writeln(sprintf(
                '<language>%s</language> is not Language, skipping...',
                $reflection->name
            ), OutputInterface::VERBOSITY_VERBOSE);
            return false;
        }

        if ($reflection->getMethod('getMetadata')->getDeclaringClass()->name !== $reflection->name) {
            $output->writeln(sprintf(
                '<language>%s</language>::<info>getAliases</info> is not declared, skipping...',
                $reflection->name
            ), OutputInterface::VERBOSITY_VERBOSE);
            return false;
        }

        $result = array_merge(
            [$reflection->name], // Class name
            array_replace(Language::getMetadata(), call_user_func([$reflection->name, 'getMetadata'])) // metadata with default values
        );
        $output->writeln(var_export($result, true), OutputInterface::VERBOSITY_VERBOSE);

        return $result;
    }
}
