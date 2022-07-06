<?php

declare(strict_types=1);

namespace Povils\PHPMND\Printer;

use PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter;
use Povils\PHPMND\DetectionResult;
use Povils\PHPMND\HintList;
use Symfony\Component\Console\Output\OutputInterface;

class Console implements Printer
{
    private Highlighter $highlighter;

    public function __construct(Highlighter $highlighter)
    {
        $this->highlighter = $highlighter;
    }

    public function printData(OutputInterface $output, HintList $hintList, array $detections): void
    {
        $index = 0;

        $lines = [];

        foreach ($this->groupDetectionResultPerFile($detections) as $detectionResults) {
            foreach ($detectionResults as $detection) {
                ++$index;

                $lines[] = $this->getDetectionLine($index, $detection);
                $lines[] = '';
                $lines[] = $this->getSnippetLine($detection);
                $lines[] = '';

                if ($hintList->hasHints()) {
                    $hints = $hintList->getHintsByValue($detection->getValue());

                    if ($hints !== []) {
                        $lines[] = 'Suggestions:';

                        foreach ($hints as $hint) {
                            $lines[] = "\t\t" . $hint;
                        }

                        $lines[] = PHP_EOL;
                    }
                }
            }
        }

        $lines[] = '';
        $lines[] = '';
        $lines[] = '<info>Total of Magic Numbers: ' . count($detections) . '</info>';

        $output->writeln($lines);
    }

    private function getDetectionLine(int $index, DetectionResult $detection): string
    {
        return sprintf(
            '%d) %s:%d    Magic number: %s',
            $index,
            $detection->getFilePath(),
            $detection->getLine(),
            $detection->getValue()
        );
    }

    private function getSnippetLine(DetectionResult $detection): string
    {
        return $this->highlighter->getCodeSnippet(
            $detection->getSource(),
            $detection->getLine(),
            0,
            0
        );
    }

    /**
     * @param array<int, DetectionResult> $detections
     *
     * @return array<int, DetectionResult[]>
     */
    private function groupDetectionResultPerFile(array $detections): array
    {
        $groupedResult = [];

        foreach ($detections as $detection) {
            $groupedResult[$detection->getFilePath()][] = $detection;
        }

        return $groupedResult;
    }
}
