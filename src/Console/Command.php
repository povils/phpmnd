<?php

namespace PHPMND\Console;

use PHPMND\Detector;
use PHPMND\ExtensionFactory;
use PHPMND\PHPFinder;
use PHPMND\Printer;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Command
 *
 * @package PHPMND\Console
 */
class Command extends BaseCommand
{
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
                'A comma-separated list of extensions',
                []
            )
            ->addOption(
                'ignore-numbers',
                null,
                InputOption::VALUE_REQUIRED,
                'A comma-separated list of numbers to ignore',
                [0, 1]
            )
            ->addOption(
                'ignore-funcs',
                null,
                InputOption::VALUE_REQUIRED,
                'A comma-separated list of functions to ignore when using "argument" extension',
                []
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
                'strings',
                null,
                InputOption::VALUE_NONE,
                'Include strings literal search in code analysis'
            )
            ->addOption(
                'ignore-strings',
                null,
                InputOption::VALUE_REQUIRED,
                'A comma-separated list of strings to ignore when using "strings" option',
                []
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new PHPFinder(
            $input->getArgument('directory'),
            $input->getOption('exclude'),
            $input->getOption('exclude-path'),
            $input->getOption('exclude-file'),
            $this->getCSVOption($input, 'suffixes')
        );

        if (0 === $finder->count()) {
            $output->writeln('No files found to scan');
            exit(1);
        }

        $progressBar = null;
        if ($input->getOption('progress')) {
            $progressBar = new ProgressBar($output, $finder->count());
            $progressBar->start();
        }

        $detector = new Detector($this->createOption($input));

        $printer = new Printer();
        foreach ($finder as $file) {
            try {
                $fileReport = $detector->detect($file);
                if ($fileReport->hasMagicNumbers()) {
                    $printer->addFileReport($fileReport);
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
            $printer->printData($output);
            $output->writeln('<info>' . \PHP_Timer::resourceUsage() . '</info>');
        }
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
        $option->setIgnoreStrings($input->getOption('ignore-strings'));
        $extensions = $this->getCSVOption($input, 'extensions');
        foreach ($extensions as $extensionName) {
            $option->addExtension(ExtensionFactory::create($extensionName));
        }

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
            return explode(',', $result);
        }

        return $result;
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
