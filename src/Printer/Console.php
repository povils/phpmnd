<?php

namespace Povils\PHPMND\Printer;

use JakubOnderka\PhpConsoleColor\ConsoleColor;
use JakubOnderka\PhpConsoleHighlighter\Highlighter;
use Povils\PHPMND\FileReportList;
use Povils\PHPMND\HintList;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Console
 *
 * @package Povils\PHPMND\Printer
 */
class Console implements Printer
{
    const DEFAULT_LINE_LENGTH = 80;

    public function printData(OutputInterface $output, FileReportList $fileReportList, HintList $hintList): void
    {
        $length = (int) (`tput cols` ?: self::DEFAULT_LINE_LENGTH);
        $separator = str_repeat('-', $length);
        $output->writeln(PHP_EOL . $separator . PHP_EOL);

        $total = 0;
        foreach ($fileReportList->getFileReports() as $fileReport) {
            $entries = $fileReport->getEntries();
            $total += count($entries);
            foreach ($entries as $entry) {
                $output->writeln(sprintf(
                    '%s:%d  Magic number: %s',
                    $fileReport->getFile()->getRelativePathname(),
                    $entry['line'],
                    $entry['value']
                ));

                $highlighter = new Highlighter(new ConsoleColor());
                $output->writeln(
                    $highlighter->getCodeSnippet($fileReport->getFile()->getContents(), $entry['line'], 0, 0)
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
