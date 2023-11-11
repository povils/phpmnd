<?php

declare(strict_types=1);

namespace Povils\PHPMND\Command;

use PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor;
use PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter;
use Povils\PHPMND\Console\Application;
use Povils\PHPMND\Console\Option;
use Povils\PHPMND\Detector;
use Povils\PHPMND\ExtensionResolver;
use Povils\PHPMND\HintList;
use Povils\PHPMND\PHPFinder;
use Povils\PHPMND\Printer;
use SebastianBergmann\Timer\ResourceUsageFormatter;
use SebastianBergmann\Timer\Timer;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method Application getApplication()
 */
class RunCommand extends BaseCommand
{
    public const SUCCESS = 0;
    public const FAILURE = 1;

    private Timer $timer;

    protected function configure(): void
    {
        $this
            ->setName('run')
            ->addArgument(
                'directories',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'One or more files and/or directories to analyze'
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
            )
            ->addOption(
                'include-numeric-string',
                null,
                InputOption::VALUE_NONE,
                'Include strings which are numeric'
            )
            ->addOption(
                'allow-array-mapping',
                null,
                InputOption::VALUE_NONE,
                'Allow array mapping (key as strings) when using "array" extension.'
            )
            ->addOption(
                'xml-output',
                null,
                InputOption::VALUE_REQUIRED,
                'Generate an XML output to the specified path'
            )
            ->addOption(
                'whitelist',
                null,
                InputOption::VALUE_REQUIRED,
                'Link to a file containing filenames to search',
                ''
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->startTimer();
        $finder = $this->createFinder($input);

        $filesCount = $finder->count();

        if ($filesCount === 0) {
            $output->writeln('No files found to scan');
            return self::SUCCESS;
        }

        $progressBar = null;
        $isProgressBarEnabled = $input->getOption('progress');

        if ($isProgressBarEnabled) {
            $progressBar = new ProgressBar($output, $filesCount);
            $progressBar->start();
        }

        $hintList = new HintList();

        $detector = new Detector(
            $this->getApplication()->getContainer()->getFileParser(),
            $this->createOption($input),
            $hintList
        );

        $whitelist = $this->getFileOption($input->getOption('whitelist'));

        $detections = [];

        foreach ($finder as $file) {
            if ($whitelist !== [] && !in_array($file->getRelativePathname(), $whitelist, true)) {
                continue;
            }

            foreach ($detector->detect($file) as $detectionResult) {
                $detections[] = $detectionResult;
            }

            if ($isProgressBarEnabled) {
                $progressBar->advance();
            }
        }

        if ($input->getOption('progress')) {
            $progressBar->finish();
        }

        if ($input->getOption('xml-output')) {
            $xmlOutput = new Printer\Xml($input->getOption('xml-output'));
            $xmlOutput->printData($output, $hintList, $detections);
        }

        if (!$output->isQuiet()) {
            $output->writeln('');
            $printer = new Printer\Console(new Highlighter(new ConsoleColor()));
            $printer->printData($output, $hintList, $detections);
            $output->writeln('<info>' . $this->getResourceUsage() . '</info>');
        }

        return $detections === [] ? self::SUCCESS : self::FAILURE;
    }

    private function createOption(InputInterface $input): Option
    {
        $option = new Option();
        $option->setIgnoreNumbers(array_map([$this, 'castToNumber'], $this->getCSVOption($input, 'ignore-numbers')));
        $option->setIgnoreFuncs($this->getCSVOption($input, 'ignore-funcs'));
        $option->setIncludeStrings($input->getOption('strings'));
        $option->setIncludeNumericStrings($input->getOption('include-numeric-string'));
        $option->setIgnoreStrings($this->getCSVOption($input, 'ignore-strings'));
        $option->setAllowArrayMapping($input->getOption('allow-array-mapping'));
        $option->setGiveHint($input->getOption('hint'));
        $option->setExtensions(
            (new ExtensionResolver())->resolve($this->getCSVOption($input, 'extensions'))
        );

        return $option;
    }

    private function getCSVOption(InputInterface $input, string $option): array
    {
        $result = $input->getOption($option);

        if (null === $result) {
            return [];
        }

        if (!is_array($result)) {
            return array_filter(explode(',', (string) $result));
        }

        return $result;
    }

    protected function createFinder(InputInterface $input): PHPFinder
    {
        return new PHPFinder(
            $input->getArgument('directories'),
            $input->getOption('exclude'),
            $input->getOption('exclude-path'),
            $input->getOption('exclude-file'),
            $this->getCSVOption($input, 'suffixes')
        );
    }

    private function castToNumber(string $value)
    {
        if (is_numeric($value)) {
            $value += 0; // '2' -> (int) 2, '2.' -> (float) 2.0
        }

        return $value;
    }

    private function getFileOption($filename): array
    {
        $filename = $this->convertFileDescriptorLink($filename);

        if (is_string($filename) && file_exists($filename)) {
            return array_map('trim', file($filename));
        }

        return [];
    }

    private function convertFileDescriptorLink($path)
    {
        if (is_string($path) && strpos($path, '/dev/fd') === 0) {
            return str_replace('/dev/fd', 'php://fd', $path);
        }

        return $path;
    }

    private function startTimer(): void
    {
        if (class_exists(ResourceUsageFormatter::class)) {
            $this->timer = new Timer();
            $this->timer->start();
        }
    }

    private function getResourceUsage(): string
    {
        // php-timer ^4.0||^5.0
        if (class_exists(ResourceUsageFormatter::class)) {
            return (new ResourceUsageFormatter)->resourceUsage($this->timer->stop());
        }

        // php-timer ^2.0||^3.0
        return Timer::resourceUsage();
    }
}
