<?php

declare(strict_types=1);

namespace Povils\PHPMND\Tests\Printer;

use Povils\PHPMND\Console\Application;
use Povils\PHPMND\DetectionResult;
use Povils\PHPMND\HintList;
use Povils\PHPMND\Printer\Xml;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Finder\SplFileInfo;

class XmlTest extends TestCase
{
    public function testEmpty() : void
    {
        $outputPath = tempnam(sys_get_temp_dir(), 'phpmnd_');

        $xmlPrinter = new Xml($outputPath);
        $xmlPrinter->printData(new NullOutput(), new HintList(), []);

        $this->assertXml(
            <<<'XML'
<?xml version="1.0"?>
<phpmnd version="%%PHPMND_VERSION%%" fileCount="0" errorCount="0"><files/></phpmnd>
XML
            ,
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
            ->willReturn(sprintf(
                '$rootNode->setAttribute(\'fileCount\', count($fileReportList->getFileReports()) + %d);',
                $testMagicNumber
            ));

        $list[] = new DetectionResult($splFileInfo, 1, $testMagicNumber);

        $hintList = new HintList();
        $hintList->addClassCont($testMagicNumber, __CLASS__, 'WELL_KNOWN_MAGIC');

        $outputPath = tempnam(sys_get_temp_dir(), 'phpmnd_');
        $xmlPrinter = new Xml($outputPath);
        $xmlPrinter->printData(new NullOutput(), $hintList, $list);

        $this->assertXml(
            <<<'XML'
<?xml version="1.0"?>
<phpmnd errorCount="1" fileCount="1" version="%%PHPMND_VERSION%%">
    <files>
        <file errors="1" path="Foo/Bar.php">
            <entry end="82" line="1" start="80">
                <snippet>
                    <![CDATA[$rootNode->setAttribute('fileCount', count($fileReportList->getFileReports()) + 12);]]>
                </snippet>
                <suggestions>
                    <suggestion>Povils\PHPMND\Tests\Printer\XmlTest::WELL_KNOWN_MAGIC</suggestion>
                </suggestions>
            </entry>
        </file>
    </files>
</phpmnd>
XML
            ,
            $outputPath
        );
    }

    private function assertXml(string $expected, string $actualFile) : void
    {
        $expectedXml = str_replace('%%PHPMND_VERSION%%', Application::VERSION, $expected);
        $this->assertXmlStringEqualsXmlString($expectedXml, file_get_contents($actualFile));
    }
}
