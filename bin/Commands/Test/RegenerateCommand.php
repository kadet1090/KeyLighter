<?php


namespace Kadet\Highlighter\bin\Commands\Test;


use Kadet\Highlighter\Formatter\FormatterInterface;
use Kadet\Highlighter\KeyLighter;
use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Parser\Tokens;
use Kadet\Highlighter\Tests\Helpers\TestFormatter;
use Kadet\Highlighter\Utils\StringHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class RegenerateCommand extends Command
{
    /**
     * @var KeyLighter
     */
    private $_keylighter;
    /** @var  \Kadet\Highlighter\Formatter\FormatterInterface */
    private $_formatter;

    private $_input;
    private $_output;

    protected function configure()
    {
        $this->setName('test:regenerate')
            ->addArgument('files', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'tests to regenerate', ['*'])
            ->addOption('new', null, InputOption::VALUE_NONE, 'generate only new files')
            ->addOption('review', 'r', InputOption::VALUE_OPTIONAL, 'review generated files')
            ->setDescription('Regenerates test files')
        ;

        $this->_keylighter = KeyLighter::get();
        $this->_formatter  = new TestFormatter();

        $this->_input  = realpath(__DIR__.'/../../../Tests/Samples');
        $this->_output = realpath(__DIR__.'/../../../Tests/Expected/Test');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $this->_input,
                \RecursiveDirectoryIterator::SKIP_DOTS | \RecursiveDirectoryIterator::UNIX_PATHS
            ), \RecursiveIteratorIterator::LEAVES_ONLY
        );

        $reviewer = $this->_keylighter->getFormatter($input->getOption('review') ?: 'cli');

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            $pathname = substr($file->getPathname(), strlen($this->_input) + 1);
            if(!$this->regenerate($input, $pathname)) {
                continue;
            }

            $output->writeln("Generating $pathname...", OutputInterface::VERBOSITY_QUIET);

            $language = Language::byFilename($pathname);
            $tokens   = $language->parse(file_get_contents($file->getPathname()));

            if ($this->review($input, $output, $tokens, $reviewer)) {
                $result = StringHelper::normalize($this->_formatter->format($tokens));

                if(!file_exists($this->_output.'/'.dirname($pathname))) {
                    mkdir($this->_output.'/'.dirname($pathname), true);
                }

                file_put_contents("{$this->_output}/$pathname.tkn", $result);
            }
        }
    }

    private function review(InputInterface $input, OutputInterface $output, Tokens $tokens, FormatterInterface $reviewer)
    {
        if (!$this->shouldReview($input)) {
            return true;
        }

        $result = $reviewer->format($tokens);
        $output->writeln($result);

        $question = new ConfirmationQuestion('Does it look right?');
        return $this->getHelper('question')->ask($input, $output, $question);
    }

    private function shouldReview(InputInterface $input)
    {
        return $input->hasParameterOption('--review')
            || $input->hasParameterOption('-r');
    }

    private function regenerate(InputInterface $input, $filename) {
        $filename = str_replace(DIRECTORY_SEPARATOR, '/', $filename);
        $patterns = $input->getArgument('files');

        foreach($patterns as $pattern) {
            if($input->getOption('new') && file_exists("{$this->_output}/$filename.tkn")) {
                continue;
            }

            if(fnmatch($pattern, $filename)) {
                return true;
            }
        }

        return false;
    }
}