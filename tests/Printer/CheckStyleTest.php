<?php

namespace Povils\PHPMND\Tests\Printer;

use Povils\PHPMND\FileReport;
use Povils\PHPMND\FileReportList;
use Povils\PHPMND\HintList;
use Povils\PHPMND\Printer\CheckStyle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class XmlTest
 *
 * @package Povils\PHPMND\Tests
 */
class CheckStyleTest extends TestCase
{
    public function testEmpty() : void
    {
        $outputPath = tempnam(sys_get_temp_dir(), 'phpmnd_');

        $xmlPrinter = new CheckStyle($outputPath);
        $xmlPrinter->printData(new NullOutput(), new FileReportList(), new HintList());

        $this->assertXmlStringEqualsXmlString(
            <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<checkstyle />
XML
            ,
            file_get_contents($outputPath)
        );
    }

    public function testPrintData() : void
    {
        $testMagicNumber = 12;

        $splFileInfo = $this->createMock(SplFileInfo::class);
        $splFileInfo
            ->method('getRelativePathname')
            ->willReturn('Foo/Bar.php');
        $splFileInfo
            ->method('getContents')
            ->willReturn(sprintf(
                '$rootNode->setAttribute(\'fileCount\', count($fileReportList->getFileReports()) + %d);',
                $testMagicNumber
            ));

        $fileReport = new FileReport($splFileInfo);
        $fileReport->addEntry(1, $testMagicNumber);
        $fileReportList = new FileReportList();
        $fileReportList->addFileReport($fileReport);

        $outputPath = tempnam(sys_get_temp_dir(), 'phpmnd_');
        $xmlPrinter = new CheckStyle($outputPath);
        $xmlPrinter->printData(new NullOutput(), $fileReportList, new HintList());

        $this->assertXmlStringEqualsXmlString(
            <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<checkstyle>
    <file name="Foo/Bar.php">
        <error line="1" column="80" severity="error" message="Magic number: 12"/>
    </file>
</checkstyle>
XML
            ,
            file_get_contents($outputPath)
        );
    }
}
