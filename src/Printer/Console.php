<?php

declare(strict_types=1);

namespace Povils\PHPMND\Printer;

use JakubOnderka\PhpConsoleColor\ConsoleColor;
use JakubOnderka\PhpConsoleHighlighter\Highlighter;
use Povils\PHPMND\FileReportList;
use Povils\PHPMND\HintList;
use Symfony\Component\Console\Output\OutputInterface;

class Console implements Printer
{
    private const DEFAULT_LINE_LENGTH = 80;
    private $decorator;

    public function __construct(Decorator $decorator)
    {
        $this->decorator = $decorator;
    }

    public function printData(OutputInterface $output, FileReportList $fileReportList, HintList $hintList): void
    {
        $length = (int) (`tput cols` ?: self::DEFAULT_LINE_LENGTH);
        $separator = str_repeat('-', $length);
        $output->writeln(PHP_EOL . $separator . PHP_EOL);

        $total = 0;

        foreach ($fileReportList->getFileReports() as $fileReport) {
            $entries = $fileReport->getEntries();
            $total += count($entries);
            $contents = $fileReport->getFile()->getContents();
            $contents = str_replace(["\r\n", "\r"], "\n", $contents);
            foreach ($entries as $entry) {
                $output->writeln(sprintf(
                    '%s:%d  Magic number: %s',
                    $fileReport->getFile()->getRelativePathname(),
                    $entry['line'],
                    $entry['value']
                ));

                $output->writeln(
                    $this->decorator->getLine($contents, $entry['line'])
                );

                if ($hintList->hasHints()) {
                    $hints = $hintList->getHintsByValue($entry['value']);
                    if (false === empty($hints)) {
                        $output->writeln('Suggestions:');
                        foreach ($hints as $hint) {
                            $output->writeln("\t\t" . $hint);
                        }
                        $output->write(PHP_EOL);
                    }
                }
            }
            $output->writeln($separator . PHP_EOL);
        }
        $output->writeln('<info>Total of Magic Numbers: ' . $total . '</info>');
    }
}
