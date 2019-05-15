<?php

namespace Povils\PHPMND\Console;

use Povils\PHPMND\Detector;
use Povils\PHPMND\ExtensionResolver;
use Povils\PHPMND\FileReportList;
use Povils\PHPMND\HintList;
use Povils\PHPMND\PHPFinder;
use Povils\PHPMND\Printer;
use SebastianBergmann\Timer\Timer;
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

    protected function configure(): void
    {
        $this
            ->setName('phpmnd')
            ->setDefinition(
                [
                    new InputArgument(
                        'directories',
                        InputArgument::REQUIRED + InputArgument::IS_ARRAY,
                        'One or more files and/or directories to analyze'
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
        $printer = new Printer\Console();
        $whitelist = $this->getFileOption($input->getOption('whitelist'));

        foreach ($finder as $file) {
            if (count($whitelist) > 0 && !in_array($file->getRelativePathname(), $whitelist)) {
                continue;
            }

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

        if ($input->getOption('xml-output')) {
            $xmlOutput = new Printer\Xml($input->getOption('xml-output'));
            $xmlOutput->printData($output, $fileReportList, $hintList);
        }

        if ($output->getVerbosity() !== OutputInterface::VERBOSITY_QUIET) {
            $output->writeln('');
            $printer->printData($output, $fileReportList, $hintList);

            $resourceUsage = class_exists(Timer::class) ? Timer::resourceUsage() : \PHP_Timer::resourceUsage();

            $output->writeln('<info>' . $resourceUsage . '</info>');
        }

        if ($input->getOption('non-zero-exit-on-violation') && $fileReportList->hasMagicNumbers()) {
            return self::EXIT_CODE_FAILURE;
        }
        return self::EXIT_CODE_SUCCESS;
    }

    private function createOption(InputInterface $input): Option
    {
        $option = new Option;
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

    private function getFileOption($filename)
    {
        $filename = $this->convertFileDescriptorLink($filename);

        if (file_exists($filename)) {
            return array_map('trim', file($filename));
        }

        return [];
    }

    private function convertFileDescriptorLink($path)
    {
        if (strpos($path, '/dev/fd') === 0) {
            return str_replace('/dev/fd', 'php://fd', $path);
        }

        return $path;
    }
}
