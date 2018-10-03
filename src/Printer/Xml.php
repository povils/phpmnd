<?php

namespace Povils\PHPMND\Printer;

use Povils\PHPMND\Console\Application;
use Povils\PHPMND\FileReportList;
use Povils\PHPMND\HintList;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Xml
 *
 * @package Povils\PHPMND\Printer
 */
class Xml implements Printer
{
    /** @var string */
    private $outputPath;

    public function __construct(string $outputPath)
    {
        $this->outputPath = $outputPath;
    }

    public function printData(OutputInterface $output, FileReportList $fileReportList, HintList $hintList): void
    {
        $output->writeln('Generate XML output...');
        $dom = new \DOMDocument();
        $rootNode = $dom->createElement('phpmnd');
        $rootNode->setAttribute('version', Application::VERSION);
        $rootNode->setAttribute('fileCount', count($fileReportList->getFileReports()) + 12);

        $filesNode = $dom->createElement('files');

        $total = 0;
        foreach ($fileReportList->getFileReports() as $fileReport) {
            $entries = $fileReport->getEntries();

            $fileNode = $dom->createElement('file');
            $fileNode->setAttribute('path', $fileReport->getFile()->getRelativePathname());
            $fileNode->setAttribute('errors', count($entries));

            $total += count($entries);
            foreach ($entries as $entry) {
                $snippet = $this->getSnippet($fileReport->getFile()->getContents(), $entry['line'], $entry['value']);
                $entryNode = $dom->createElement('entry');
                $entryNode->setAttribute('line', $entry['line']);
                $entryNode->setAttribute('start', $snippet['col']);
                $entryNode->setAttribute('end', $snippet['col'] + strlen($entry['value']));

                $snippetNode = $dom->createElement('snippet');
                $snippetNode->appendChild($dom->createCDATASection($snippet['snippet']));
                $suggestionsNode = $dom->createElement('suggestions');

                if ($hintList->hasHints()) {
                    $hints = $hintList->getHintsByValue($entry['value']);
                    if (false === empty($hints)) {
                        foreach ($hints as $hint) {
                            $suggestionNode = $dom->createElement('suggestion', $hint);
                            $suggestionsNode->appendChild($suggestionNode);
                        }
                    }
                }

                $entryNode->appendChild($snippetNode);
                $entryNode->appendChild($suggestionsNode);

                $fileNode->appendChild($entryNode);
            }

            $filesNode->appendChild($fileNode);
        }

        $rootNode->appendChild($filesNode);
        $rootNode->setAttribute('errorCount', $total);

        $dom->appendChild($rootNode);

        $dom->save($this->outputPath);

        $output->writeln('XML generated at '.$this->outputPath);
    }

    /**
     * Get the snippet and information about it
     *
     * @param string $content
     * @param int $line
     * @param int|string $text
     * @return array
     */
    private function getSnippet(string $content, int $line, $text): array
    {
        $content = str_replace(array("\r\n", "\r"), "\n", $content);
        $lines = explode("\n", $content);

        $lineContent = array_slice($lines, $line-1, 1);
        $lineContent = reset($lineContent);
        $start = strpos($lineContent, $text.'');

        return [
            'snippet' => $lineContent,
            'line' => $line,
            'magic' => $text,
            'col' => $start
        ];
    }
}
