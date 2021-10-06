<?php

declare(strict_types=1);

namespace Povils\PHPMND\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Povils\PHPMND\FileReport;
use Povils\PHPMND\FileReportList;

class FileReportListTest extends TestCase
{
    public function testAddFileReport(): void
    {
        $fileReportList = new FileReportList;
        /** @var FileReport&MockObject $fileReport */
        $fileReport = $this->createMock(FileReport::class);
        $fileReport
            ->method('hasMagicNumbers')
            ->willReturn(true);

        $fileReportList->addFileReport($fileReport);

        $this->assertSame([$fileReport], $fileReportList->getFileReports());
        $this->assertTrue($fileReportList->hasMagicNumbers());
    }

    public function testDoesNotHaveMagicNumbers(): void
    {
        $fileReportList = new FileReportList;

        $this->assertFalse($fileReportList->hasMagicNumbers());
    }
}
