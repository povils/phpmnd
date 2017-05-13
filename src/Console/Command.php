<?php

namespace Povils\PHPMND\Console;

use Povils\PHPMND\Detector;
use Povils\PHPMND\ExtensionResolver;
use Povils\PHPMND\FileReportList;
use Povils\PHPMND\HintList;
use Povils\PHPMND\PHPFinder;
use Povils\PHPMND\Printer;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Command
 *
 * @package Povils\PHPMND\Console
 */
class Command extends BaseCommand
{
    const EXIT_CODE_SUCCESS = 0;
    const EXIT_CODE_FAILURE = 1;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('phpmnd')
            ->setDefinition(
                [
                    new InputArgument(
                        'directory',
                        InputArgument::REQUIRED,
                        'Directory to analyze'
                    )
                ]
            )
            ->addOption(
                'extensions',
                null,
                InputOption::VALUE_REQUIRED,
                'A comma-separated list of extensions'
            )
            ->addOption(
                'ignore-numbers',
                null,
                InputOption::VALUE_REQUIRED,
                'A comma-separated list of numbers to ignore',
                '0, 1'
            )
            ->addOption(
                'ignore-funcs',
                null,
                InputOption::VALUE_REQUIRED,
                'A comma-separated list of functions to ignore when using "argument" extension'
            )
            ->addOption(
                'exclude',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Exclude a directory from code analysis (must be relative to source)'
            )
            ->addOption(
                'exclude-path',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Exclude a path from code analysis (must be relative to source)'
            )
            ->addOption(
                'exclude-file',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Exclude a file from code analysis (must be relative to source)'
            )
            ->addOption(
                'suffixes',
                null,
                InputOption::VALUE_REQUIRED,
                'Comma-separated string of valid source code filename extensions',
                'php'
            )
            ->addOption(
                'progress',
                null,
                InputOption::VALUE_NONE,
                'Show progress bar'
            )
            ->addOption(
                'hint',
                null,
                InputOption::VALUE_NONE,
                'Suggest replacements for magic numbers'
            )
            ->addOption(
                'non-zero-exit-on-violation',
                null,
                InputOption::VALUE_NONE,
                'Return a non zero exit code when there are magic numbers'
            )
            ->addOption(
                'strings',
                null,
                InputOption::VALUE_NONE,
                'Include strings literal search in code analysis'
            )
            ->addOption(
                'ignore-strings',
                null,
                InputOption::VALUE_REQUIRED,
                'A comma-separated list of strings to ignore when using "strings" option'
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = $this->createFinder($input);

        if (0 === $finder->count()) {
            $output->writeln('No files found to scan');
            return self::EXIT_CODE_SUCCESS;
        }

        $progressBar = null;
        if ($input->getOption('progress')) {
            $progressBar = new ProgressBar($output, $finder->count());
            $progressBar->start();
        }

        $hintList = new HintList;
        $detector = new Detector($this->createOption($input), $hintList);

        $fileReportList = new FileReportList();
        $printer = new Printer();
        foreach ($finder as $file) {
            try {
                $fileReport = $detector->detect($file);
                if ($fileReport->hasMagicNumbers()) {
                    $fileReportList->addFileReport($fileReport);
                }
            } catch (\Exception $e) {
                $output->writeln($e->getMessage());
            }

            if ($input->getOption('progress')) {
                $progressBar->advance();
            }
        }

        if ($input->getOption('progress')) {
            $progressBar->finish();
        }

        if ($output->getVerbosity() !== OutputInterface::VERBOSITY_QUIET) {
            $output->writeln('');
            $printer->printData($output, $fileReportList, $hintList);
            $output->writeln('<info>' . \PHP_Timer::resourceUsage() . '</info>');
        }

        if ($input->getOption('non-zero-exit-on-violation') && $fileReportList->hasMagicNumbers()) {
            return self::EXIT_CODE_FAILURE;
        }
        return self::EXIT_CODE_SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @return Option
     * @throws \Exception
     */
    private function createOption(InputInterface $input)
    {
        $option = new Option;
        $option->setIgnoreNumbers(array_map([$this, 'castToNumber'], $this->getCSVOption($input, 'ignore-numbers')));
        $option->setIgnoreFuncs($this->getCSVOption($input, 'ignore-funcs'));
        $option->setIncludeStrings($input->getOption('strings'));
        $option->setIgnoreStrings($this->getCSVOption($input, 'ignore-strings'));
        $option->setGiveHint($input->getOption('hint'));
        $option->setExtensions(
            (new ExtensionResolver())->resolve($this->getCSVOption($input, 'extensions'))
        );

        return $option;
    }

    /**
     * @param InputInterface $input
     * @param string $option
     *
     * @return array
     */
    private function getCSVOption(InputInterface $input, $option)
    {
        $result = $input->getOption($option);
        if (false === is_array($result)) {
            return array_filter(
                explode(',', $result),
                function ($value) {
                    return false === empty($value);
                }
            );
        }

        if (null === $result) {
            return [];
        }

        return $result;
    }

    /**
     * @param InputInterface $input
     *
     * @return PHPFinder
     */
    protected function createFinder(InputInterface $input)
    {
        return new PHPFinder(
            $input->getArgument('directory'),
            $input->getOption('exclude'),
            $input->getOption('exclude-path'),
            $input->getOption('exclude-file'),
            $this->getCSVOption($input, 'suffixes')
        );
    }

    /**
     * @param string $value
     *
     * @return int|float|string
     */
    private function castToNumber($value)
    {
        if (is_numeric($value)) {
            $value += 0; // '2' -> (int) 2, '2.' -> (float) 2.0
        }

        return $value;
    }
}
