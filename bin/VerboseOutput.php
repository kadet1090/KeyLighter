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

use Kadet\Highlighter\Formatter\DebugFormatter;
use Kadet\Highlighter\Formatter\FormatterInterface;
use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Parser\Tokens;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VerboseOutput
{
    /** @var OutputInterface */
    private $_output;
    /** @var InputInterface */
    private $_input;

    private $_source;
    /** @var Language */
    private $_language;

    private $_counts = ['before' => null, 'after' => null];
    private $_times  = ['tokenization' => null, 'parsing' => null, 'formatting' => null];

    /** @var DebugFormatter */
    private $_debug;
    /** @var FormatterInterface */
    private $_formatter;

    public function __construct(
        OutputInterface $output,
        InputInterface $input,
        Language $language,
        FormatterInterface $formatter,
        string $source
    ) {
        $this->_output = $output;
        $this->_input  = $input;

        $this->_source    = $source;
        $this->_debug     = new DebugFormatter();
        $this->_formatter = $formatter;

        $this->_language = $language;
    }

    public function process()
    {
        $tokenized = $this->tokenize();
        $parsed    = $this->parse($tokenized);
        $formatted = $this->format($parsed);

        if ($this->wants('time')) {
            $this->_output->writeln(sprintf(
                '<info>Overall:</info> %.4fs, %s chars/s',
                array_sum($this->_times),
                number_format(strlen($this->_source) / array_sum($this->_times))
            ));
        }

        if ($this->wants('detailed-time')) {
            $this->_slashed('Times             [s]', array_map(function ($t) {
                return number_format($t, 5);
            }, $this->_times));
            $this->_slashed('Performance [chars/s]', array_map(function ($t) {
                return number_format(strlen($this->_source) / $t);
            }, $this->_times));
        }

        if ($this->wants('count')) {
            $this->_slashed('Token count', array_map('number_format', $this->_counts));
        }

        if ($this->wants('density')) {
            $this->_slashed('Token density [tokens/kB]', array_map(function ($c) {
                return number_format($c / strlen($this->_source) * 1024, 1);
            }, $this->_counts));
        }

        return $formatted;
    }

    public function wants($feature)
    {
        return in_array($feature, $this->_input->getOption('debug'));
    }

    protected function tokenize()
    {
        $tokens = $this->benchmark(function () {
            return $this->_language->tokenize($this->_source);
        }, $this->_times['tokenization']);

        $this->_counts['before'] = count($tokens);
        if ($this->wants('tree-before')) {
            $this->_tree($tokens, 'before parsing', false);
        }

        return $tokens;
    }

    protected function benchmark(callable $callable, &$time = null)
    {
        $start  = microtime(true);
        $return = $callable();
        $time   = microtime(true) - $start;

        return $return;
    }

    private function _tree(Tokens $tree, $message, $indented = true)
    {
        $this->_output->writeln("Token tree <comment>$message</comment>: ");
        $this->_output->writeln($this->_debug->format(clone $tree, $indented));
    }

    protected function parse(Tokens $tokens)
    {
        $tokens = $this->benchmark(function () use ($tokens) {
            return $this->_language->parse($tokens);
        }, $this->_times['parsing']);

        $this->_counts['after'] = count($tokens);
        if ($this->wants('tree-after')) {
            $this->_tree($tokens, 'after parsing', true);
        }

        return $tokens;
    }

    protected function format(Tokens $tokens)
    {
        return $this->benchmark(function () use ($tokens) {
            return $this->_formatter->format($tokens);
        }, $this->_times['formatting']);
    }

    private function _slashed($message, $data)
    {
        $this->_output->writeln(sprintf(
            "$message %s: %s",
            implode(' / ', array_map(function ($f) {
                return "<comment>{$f}</comment>";
            }, array_keys($data))),
            implode(' / ', array_map(function ($f) {
                return "<info>{$f}</info>";
            }, array_values($data)))
        ));
    }
}
