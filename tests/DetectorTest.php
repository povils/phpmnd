<?php

namespace Povils\PHPMND\Tests;

use Povils\PHPMND\Detector;
use Povils\PHPMND\Extension\ArgumentExtension;
use Povils\PHPMND\Extension\ArrayExtension;
use Povils\PHPMND\Extension\AssignExtension;
use Povils\PHPMND\Extension\DefaultParameterExtension;
use Povils\PHPMND\Extension\OperationExtension;
use Povils\PHPMND\Extension\PropertyExtension;

/**
 * Class DetectorTest
 *
 * @package Povils\PHPMND\Tests
 */
class DetectorTest extends \PHPUnit_Framework_TestCase
{
    public function testDetectDefault()
    {
        $detector = new Detector();
        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertSame(
            [
                [
                    'line' => 14,
                    'value' => 2,
                ],
                [
                    'line' => 15,
                    'value' => 15,
                ],
                [
                    'line' => 18,
                    'value' => 10,
                ],
                [
                    'line' => 20,
                    'value' => 5,
                ],
                [
                    'line' => 26,
                    'value' => 7,
                ],
                [
                    'line' => 31,
                    'value' => 18,
                ],
            ],
            $fileReport->getEntries()
        );
    }

    public function testDetectWithAssignExtension()
    {
        $detector = new Detector();
        $detector->addExtension(new AssignExtension());
        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 5,
                'value' => 4,
            ],
            $fileReport->getEntries()
        );
    }

    public function testDetectWithPropertyExtension()
    {
        $detector = new Detector();
        $detector->addExtension(new PropertyExtension());
        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 11,
                'value' => 6,
            ],
            $fileReport->getEntries()
        );
    }

    public function testDetectWithArrayExtension()
    {
        $detector = new Detector();
        $detector->addExtension(new ArrayExtension());
        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 30,
                'value' => 13,
            ],
            $fileReport->getEntries()
        );
    }

    public function testDetectWithArgumentExtension()
    {
        $detector = new Detector();
        $detector->addExtension(new ArgumentExtension());
        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 25,
                'value' => 4,
            ],
            $fileReport->getEntries()
        );
    }

    public function testDetectWithDefaultParameterExtension()
    {
        $detector = new Detector();
        $detector->addExtension(new DefaultParameterExtension());
        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 13,
                'value' => 4,
            ],
            $fileReport->getEntries()
        );
    }

    public function testDetectWithOperationExtension()
    {
        $detector = new Detector();
        $detector->addExtension(new OperationExtension());
        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 37,
                'value' => 15,
            ],
            $fileReport->getEntries()
        );
    }

    public function testDetectWithIgnoreNumber()
    {
        $detector = new Detector();
        $ignoreNumbers = [2, 10];
        $detector->setIgnoreNumbers($ignoreNumbers);
        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        foreach ($fileReport->getEntries() as $entry) {
            $this->assertFalse(in_array($entry['value'], $ignoreNumbers, true));
        }
    }
}
