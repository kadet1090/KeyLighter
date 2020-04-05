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

namespace Kadet\Highlighter\bin\Commands\Benchmark;


use Kadet\Highlighter\Formatter\FormatterInterface;
use Kadet\Highlighter\KeyLighter;
use Kadet\Highlighter\Language\Language;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AnalyzeCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $json = json_decode(file_get_contents($input->getArgument('input')[0]), true);

        $output->writeln(sprintf(
            "Date: <info>%s</info> Formatter: <info>%s</info>, Comment: <info>%s</info>",
            date('d.m.Y H:i:s', $json['timestamp']), $json['formatter'], isset($json['comment']) ? $json['comment'] : 'none'
        ));

        $table = new Table($output);

        $suffix = $input->getOption('relative') ? 'bytes/s' : 'ms';
        $table->addRow(['set', "min [$suffix]", "avg [$suffix]", "max [$suffix]", "std dev [$suffix]"]);

        $summary = [];
        foreach ($json['results'] as $file => $data) {
            $this->separator($file, $table);

            foreach ($data['times'] as $set => $times) {
                $result = array_map(function ($time) use ($data, $input) {
                    return $input->getOption('relative') ? $data['size'] / $time : $time * 1000;
                }, $times);

                $this->entry($result, $set, $table);

                $summary[$set][] = array_sum($result) / count($result);
            }

            if (!isset($data['memory'])) {
                continue;
            }

            foreach ($data['memory'] as $set => $memory) {
                $result = array_map(function ($memory) use ($data, $input) {
                    $bytes = $input->getOption('relative') ? $memory/$data['size'] : $memory;
                    return $this->formatBytes($bytes, (bool)$input->getOption('relative'));
                }, $memory);

                $this->entry($result, $set, $table);
            }
        }

        if(!$input->hasParameterOption('--summary')) {
            $table->render();
        }

        $summary = array_filter($summary, function($key) use ($input) {
            return fnmatch($input->getOption('summary') ?: '*', $key);
        }, ARRAY_FILTER_USE_KEY);

        $max = max(array_map('strlen', array_keys($summary)));
        foreach($summary as $name => $set) {
            $output->writeln(sprintf(
                "<comment>%s</comment> %s %s",
                str_pad($name, $max, ' ', STR_PAD_LEFT),
                $this->format($input->getOption('relative') ? array_sum($set) / count($set) : array_sum($set)),
                $suffix
            ));
        }
    }

    protected function separator($file, Table $table)
    {
        $table->addRows([
            new TableSeparator(),
            [new TableCell($file, ['colspan' => 5])],
            new TableSeparator(),
        ]);
    }

    protected function entry($result, $set, Table $table)
    {
        $min = min($result);
        $avg = $this->avarage($result);
        $max = max($result);
        $dev = $this->stddev($result);

        $table->addRow([
            $set,
            $this->format($min),
            $this->format($avg),
            $this->format($max),
            sprintf("%s (%d%%)", $this->format($dev), $dev / $avg * 100),
        ]);
    }

    private function format($number)
    {
        return is_numeric($number) ? number_format($number, 2) : $number;
    }

    private function avarage(array $result)
    {
        return array_sum($result) / count($result);
    }

    private function stddev($result)
    {
        $mean = array_sum($result) / count($result);

        return sqrt(array_sum(array_map(function ($result) use ($mean) {
            return pow((float) $result - $mean, 2);
        }, $result)) / count($result));
    }

    function formatBytes($bytes, $relative = false) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return $this->format($bytes);//.$units[$pow].($relative ? '/byte' : '');
    }

    protected function configure()
    {
        $this
            ->setName('benchmark:analyze')
            ->setDescription('Tests performance of KeyLighter')
            ->addArgument('input', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Input JSON file(s)')
            ->addOption('relative', 'r', InputOption::VALUE_NONE, 'Show relative times?')
            ->addOption('summary', 'u', InputOption::VALUE_OPTIONAL, 'Show summary times?', '*');
    }
}
