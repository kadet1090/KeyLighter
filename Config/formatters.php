<?php return [
    'latex' => new \Kadet\Highlighter\Formatter\LatexFormatter(),
    'html'  => new \Kadet\Highlighter\Formatter\HtmlFormatter(),
    'lhtml' => new \Kadet\Highlighter\Formatter\LineContainedHtmlFormatter(),
    'cli'   => new \Kadet\Highlighter\Formatter\CliFormatter(),
    'debug' => new \Kadet\Highlighter\Formatter\DebugFormatter()
];
