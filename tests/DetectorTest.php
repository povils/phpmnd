<?php

namespace Povils\PHPMND\Tests;

use Povils\PHPMND\Console\Option;
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
        $detector = new Detector(new Option);
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
        $option = new Option;
        $option->addExtension(new AssignExtension());
        $detector = new Detector($option);
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
        $option = new Option;
        $option->addExtension(new PropertyExtension());
        $detector = new Detector($option);
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
        $option = new Option;
        $option->addExtension(new ArrayExtension());
        $detector = new Detector($option);
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
        $option = new Option;
        $option->addExtension(new ArgumentExtension());
        $detector = new Detector($option);

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
        $option = new Option;
        $option->addExtension(new DefaultParameterExtension());
        $detector = new Detector($option);

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
        $option = new Option;
        $option->addExtension(new OperationExtension());
        $detector = new Detector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 37,
                'value' => 15,
            ],
            $fileReport->getEntries()
        );

       $this->assertNotContains(
          [
             'line' => 40,
             'value' => 21,
          ],
          $fileReport->getEntries()
       );
    }

    public function testDetectWithIgnoreNumber()
    {
        $ignoreNumbers = [2, 10];
        $option = new Option;
        $option->setIgnoreNumbers($ignoreNumbers);
        $detector = new Detector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        foreach ($fileReport->getEntries() as $entry) {
            $this->assertFalse(in_array($entry['value'], $ignoreNumbers, true));
        }
    }

    public function testDetectWithIgnoreFuncs()
    {
        $ignoreFuncs = ['round'];
        $option = new Option;
        $option->addExtension(new ArgumentExtension());
        $option->setIgnoreFuncs($ignoreFuncs);
        $detector = new Detector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertNotContains(
            [
                'line' => 25,
                'value' => 4,
            ],
            $fileReport->getEntries()
        );
    }
}
