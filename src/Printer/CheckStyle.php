<?php

namespace Povils\PHPMND\Printer;

use Povils\PHPMND\FileReportList;
use Povils\PHPMND\HintList;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CheckStyle
 *
 * @package Povils\PHPMND\Printer
 */
class CheckStyle implements Printer
{
    /** @var string */
    private $outputPath;

    public function __construct(string $outputPath)
    {
        $this->outputPath = $outputPath;
    }

    public function printData(OutputInterface $output, FileReportList $fileReportList, HintList $hintList): void
    {
        $output->writeln('Generate checkstyle report output...');
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $rootNode = $dom->createElement('checkstyle');

        $total = 0;
        foreach ($fileReportList->getFileReports() as $fileReport) {
            $fileNode = $dom->createElement('file');
            $fileNode->setAttribute('name', $fileReport->getFile()->getRelativePathname());

            $entries = $fileReport->getEntries();
            $total += count($entries);
            foreach ($entries as $entry) {
                $snippet = Util::getSnippet($fileReport->getFile()->getContents(), $entry['line'], $entry['value']);
                $errorNode = $dom->createElement('error');
                $errorNode->setAttribute('line', $entry['line']);
                $errorNode->setAttribute('column', $snippet['col']);
                $errorNode->setAttribute('severity', 'error');
                $errorNode->setAttribute('message', sprintf('Magic number: %s', $entry['value']));

                $fileNode->appendChild($errorNode);
            }
            $rootNode->appendChild($fileNode);
        }

        $dom->appendChild($rootNode);

        $dom->save($this->outputPath);

        $output->writeln('Total of Magic Numbers ' . $total);
        $output->writeln('checkstyle XML generated at '. $this->outputPath);
    }
}
