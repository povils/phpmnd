<?php

namespace Povils\PHPMND\Console;

use Povils\PHPMND\Detector;
use Povils\PHPMND\ExtensionFactory;
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
                'exclude',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Exclude a directory from code analysis (must be relative to source)'
            )
            ->addOption(
                'progress',
                null,
                InputOption::VALUE_NONE,
                'Show progress bar'
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new PHPFinder();
        $finder
            ->in($input->getArgument('directory'))
            ->exclude(array_merge(['vendor'], $input->getOption('exclude')));

        if (0 === $finder->count()) {
            $output->writeln('No files found to scan');
            exit(1);
        }

        if ($input->getOption('progress')) {
            $progressBar = new ProgressBar($output, $finder->count());
            $progressBar->start();
        }

        $detector = new Detector();
        $extensions = $this->getCSVOption($input, 'extensions');
        foreach ($extensions as $extensionName) {
            $detector->addExtension(ExtensionFactory::create($extensionName));
        }

        $detector->setIgnoreNumbers($this->getCSVOption($input, 'ignore-numbers'));


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
     * @param string $option
     *
     * @return array
     */
    private function getCSVOption(InputInterface $input, $option)
    {
        $result = $input->getOption($option);
        if (false === is_array($result)) {
            $result = explode(',', $result);
            $result = array_map([$this, 'castToNumber'], $result);
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
            $value += 0; // '2' => (int) 2 ; '2.' => (float) 2.0
        }

        return $value;
    }
}
