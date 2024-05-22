<?php

declare(strict_types=1);

namespace Povils\PHPMND\Printer;

use DOMDocument;
use Povils\PHPMND\Console\Application;
use Povils\PHPMND\DetectionResult;
use Povils\PHPMND\HintList;
use Symfony\Component\Console\Output\OutputInterface;

class Xml implements Printer
{
    private string $outputPath;

    public function __construct(string $outputPath)
    {
        $this->outputPath = $outputPath;
    }

    /**
     * @param array<int, DetectionResult> $detections
     */
    public function printData(OutputInterface $output, HintList $hintList, array $detections): void
    {
        $groupedList = $this->groupDetectionResultPerFile($detections);

        $output->writeln('Generate XML output...');
        $dom = new DOMDocument();
        $rootNode = $dom->createElement('phpmnd');
        $rootNode->setAttribute('version', Application::getPrettyVersion());
        $rootNode->setAttribute('fileCount', (string) count($groupedList));

        $filesNode = $dom->createElement('files');

        $total = 0;

        foreach ($groupedList as $path => $detectionResults) {
            $count = count($detectionResults);
            $total += $count;

            $fileNode = $dom->createElement('file');
            $fileNode->setAttribute('path', $path);
            $fileNode->setAttribute('errors', (string) $count);

            foreach ($detectionResults as $detectionResult) {
                $snippet = $this->getSnippet(
                    $detectionResult->getFile()->getContents(),
                    $detectionResult->getLine(),
                    $detectionResult->getValue()
                );
                $entryNode = $dom->createElement('entry');
                $entryNode->setAttribute('line', (string) $detectionResult->getLine());
                $entryNode->setAttribute('start', (string) $snippet['col']);
                $entryNode->setAttribute(
                    'end',
                    (string) ($snippet['col'] + strlen((string) $detectionResult->getValue()))
                );

                $snippetNode = $dom->createElement('snippet');
                $snippetNode->appendChild($dom->createCDATASection($snippet['snippet']));

                $suggestionsNode = $dom->createElement('suggestions');

                if ($hintList->hasHints()) {
                    $hints = $hintList->getHintsByValue($detectionResult->getValue());

                    foreach ($hints as $hint) {
                        $suggestionNode = $dom->createElement('suggestion', $hint);
                        $suggestionsNode->appendChild($suggestionNode);
                    }
                }

                $entryNode->appendChild($snippetNode);
                $entryNode->appendChild($suggestionsNode);

                $fileNode->appendChild($entryNode);
            }

            $filesNode->appendChild($fileNode);
        }

        $rootNode->appendChild($filesNode);
        $rootNode->setAttribute('errorCount', (string) $total);

        $dom->appendChild($rootNode);

        $dom->save($this->outputPath);

        $output->writeln('XML generated at ' . $this->outputPath);
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

    /**
     * Get the snippet and information about it
     *
     * @param int|string $text
     */
    private function getSnippet(string $content, int $line, $text): array
    {
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $lines = explode("\n", $content);

        $lineContent = array_slice($lines, $line - 1, 1);
        $lineContent = reset($lineContent);
        $start = strpos($lineContent, $text . '');

        return [
            'snippet' => $lineContent,
            'line' => $line,
            'magic' => $text,
            'col' => $start,
        ];
    }
}
