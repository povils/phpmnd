<?php

namespace Povils\PHPMND\Tests\Printer;

use Povils\PHPMND\Console\Application;
use Povils\PHPMND\FileReport;
use Povils\PHPMND\FileReportList;
use Povils\PHPMND\HintList;
use Povils\PHPMND\Printer\Xml;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class XmlTest
 *
 * @package Povils\PHPMND\Tests
 */
class XmlTest extends TestCase
{
    public function testEmpty() : void
    {
        $outputPath = tempnam(sys_get_temp_dir(), rawurlencode(__CLASS__));

        $xmlPrinter = new Xml($outputPath);
        $xmlPrinter->printData(new NullOutput(), new FileReportList(), new HintList());

        $this->assertXml(
            <<<'XML'
<?xml version="1.0"?>
<phpmnd version="%%PHPMND_VERSION%%" fileCount="0" errorCount="0"><files/></phpmnd>
XML,
            $outputPath
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
            ->willReturn(sprintf('$rootNode->setAttribute(\'fileCount\', count($fileReportList->getFileReports()) + %d);', $testMagicNumber));

        $fileReport = new FileReport($splFileInfo);
        $fileReport->addEntry(1, $testMagicNumber);
        $fileReportList = new FileReportList();
        $fileReportList->addFileReport($fileReport);

        $hintList = new HintList();
        $hintList->addClassCont($testMagicNumber, __CLASS__, 'WELL_KNOWN_MAGIC');

        $outputPath = tempnam(sys_get_temp_dir(), rawurlencode(__CLASS__));
        $xmlPrinter = new Xml($outputPath);
        $xmlPrinter->printData(new NullOutput(), $fileReportList, $hintList);

        $this->assertXml(
            <<<'XML'
<?xml version="1.0"?>
<phpmnd errorCount="1" fileCount="1" version="%%PHPMND_VERSION%%">
    <files>
        <file errors="1" path="Foo/Bar.php">
            <entry end="82" line="1" start="80">
                <snippet><![CDATA[$rootNode->setAttribute('fileCount', count($fileReportList->getFileReports()) + 12);]]></snippet>
                <suggestions>
                    <suggestion>Povils\PHPMND\Tests\Printer\XmlTest::WELL_KNOWN_MAGIC</suggestion>
                </suggestions>
            </entry>
        </file>
    </files>
</phpmnd>
XML,
            $outputPath
        );
    }

    private function assertXml(string $expected, string $actualFile, string $message = '') : void
    {
        $expectedXml = str_replace('%%PHPMND_VERSION%%', Application::VERSION, $expected);
        $this->assertXmlStringEqualsXmlString($expectedXml, file_get_contents($actualFile), $message);
    }
}
