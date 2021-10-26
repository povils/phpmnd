<?php

declare(strict_types=1);

namespace Povils\PHPMND\Printer;

use JakubOnderka\PhpConsoleHighlighter\Highlighter;
use Povils\PHPMND\DetectionResult;
use Povils\PHPMND\HintList;
use Symfony\Component\Console\Output\OutputInterface;

class Console implements Printer
{
    private const DEFAULT_LINE_LENGTH = 80;

    /**
     * @var Highlighter
     */
    private $highlighter;

    public function __construct(Highlighter $highlighter)
    {
        $this->highlighter = $highlighter;
    }

    public function printData(OutputInterface $output, HintList $hintList, array $list): void
    {
        $length = (int) (`tput cols` ?: self::DEFAULT_LINE_LENGTH);
        $separator = str_repeat('-', $length);
        $output->writeln(PHP_EOL . $separator . PHP_EOL);

        foreach ($this->groupDetectionResultPerFile($list) as $detectionResults) {
            foreach ($detectionResults as $detection) {
                $output->writeln(sprintf(
                    '%s:%d. Magic number: %s',
                    $detection->getFile()->getRelativePathname(),
                    $detection->getLine(),
                    $detection->getValue()
                ));

                $output->writeln(
                    $this->highlighter->getCodeSnippet(
                        $detection->getFile()->getContents(),
                        $detection->getLine(),
                        0,
                        0
                    )
                );

                if ($hintList->hasHints()) {
                    $hints = $hintList->getHintsByValue($detection->getValue());

                    if ($hints !== []) {
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

        $output->writeln('<info>Total of Magic Numbers: ' . count($list) . '</info>');
    }

    /**
     * @param array<int, DetectionResult> $list
     *
     * @return array<int, DetectionResult[]>
     */
    private function groupDetectionResultPerFile(array $list): array
    {
        $result = [];

        foreach ($list as $detectionResult) {
            $result[$detectionResult->getFile()->getRelativePathname()][] = $detectionResult;
        }

        return $result;
    }
}
