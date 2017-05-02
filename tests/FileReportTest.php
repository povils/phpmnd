<?php

namespace Povils\PHPMND\Tests;

use Povils\PHPMND\FileReport;
use Symfony\Component\Finder\SplFileInfo;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Class FileReportTest
 *
 * @package Povils\PHPMND\Tests
 */
class FileReportTest extends PHPUnitTestCase
{
    public function testFileReport()
    {
        $file = self::getTestFile('test_1');
        $fileReport = new FileReport($file);
        $fileReport->addEntry(1, 1);

        $this->assertSame(
            [
                [
                    'line' => 1,
                    'value' => 1,
                ],
            ],
            $fileReport->getEntries()
        );

        $this->assertSame($file, $fileReport->getFile());
        $this->assertTrue($fileReport->hasMagicNumbers());
    }

    /**
     * @param string $name
     *
     * @return SplFileInfo
     */
    public static function getTestFile($name)
    {
        return new SplFileInfo(
            __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . "$name.php",
            'tests' . DIRECTORY_SEPARATOR . 'files',
            'tests' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . "$name.php"
        );
    }
}
