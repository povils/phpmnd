<?php

namespace Povils\PHPMND\Tests;

use Povils\PHPMND\Console\Option;
use Povils\PHPMND\Detector;
use Povils\PHPMND\Extension\ArgumentExtension;
use Povils\PHPMND\Extension\ArrayExtension;
use Povils\PHPMND\Extension\ArrayMappingExtension;
use Povils\PHPMND\Extension\AssignExtension;
use Povils\PHPMND\Extension\ConditionExtension;
use Povils\PHPMND\Extension\DefaultParameterExtension;
use Povils\PHPMND\Extension\Extension;
use Povils\PHPMND\Extension\OperationExtension;
use Povils\PHPMND\Extension\PropertyExtension;
use PHPUnit\Framework\TestCase;
use Povils\PHPMND\Extension\ReturnExtension;
use Povils\PHPMND\Extension\SwitchCaseExtension;
use Povils\PHPMND\HintList;

/**
 * Class DetectorTest
 *
 * @package Povils\PHPMND\Tests
 */
class DetectorTest extends TestCase
{
    public function testDetectDefault()
    {
        $detector = $this->createDetector($this->createOption());
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
                [
                    'line' => 49,
                    'value' => -2,
                ],
            ],
            $fileReport->getEntries()
        );
    }

    public function testDetectWithAssignExtension()
    {
        $option = $this->createOption([new AssignExtension()]);
        $option->setIncludeStrings(true);
        $detector = $this->createDetector($option);
        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 5,
                'value' => '4',
            ],
            $fileReport->getEntries()
        );
    }

    public function testDetectWithPropertyExtension()
    {
        $option = $this->createOption([new PropertyExtension()]);
        $detector = $this->createDetector($option);
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
        $option = $this->createOption([new ArrayExtension()]);
        $detector = $this->createDetector($option);
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
        $option = $this->createOption([new ArgumentExtension()]);
        $detector = $this->createDetector($option);

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
        $option = $this->createOption([new DefaultParameterExtension()]);
        $detector = $this->createDetector($option);

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
        $option = $this->createOption([new OperationExtension()]);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 39,
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
        $option = $this->createOption();
        $option->setIgnoreNumbers($ignoreNumbers);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        foreach ($fileReport->getEntries() as $entry) {
            $this->assertFalse(in_array($entry['value'], $ignoreNumbers, true));
        }
    }

    public function testDetectWithIgnoreFuncs()
    {
        $ignoreFuncs = ['round'];
        $option = $this->createOption([new ArgumentExtension()]);
        $option->setIgnoreFuncs($ignoreFuncs);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertNotContains(
            [
                'line' => 25,
                'value' => 4,
            ],
            $fileReport->getEntries()
        );
    }

    public function testDetectIncludeStrings()
    {
        $option = $this->createOption();
        $option->setIncludeStrings(true);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 45,
                'value' => 'string',
            ],
            $fileReport->getEntries()
        );
    }

    public function testDetectIncludeStringsAndIgnoreString()
    {
        $option = $this->createOption();
        $option->setIncludeStrings(true);
        $option->setIgnoreStrings(['string']);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertNotContains(
            [
                'line' => 43,
                'value' => 'string',
            ],
            $fileReport->getEntries()
        );
    }

    public function testDetectWithHint()
    {
        $option = $this->createOption();
        $option->setExtensions([new AssignExtension]);
        $option->setGiveHint(true);
        $hintList = new HintList;
        $detector = $this->createDetector($option, $hintList);

        $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertTrue($hintList->hasHints());
        $this->assertSame(['TEST_1::TEST_1'], $hintList->getHintsByValue(3));
    }

    public function testDetectArrayMappings()
    {
        $option = $this->createOption();
        $option->setExtensions([new ArrayMappingExtension]);

        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 32,
                'value' => 18,
            ],
            $fileReport->getEntries()
        );

        $this->assertContains(
            [
                'line' => 33,
                'value' => 1234,
            ],
            $fileReport->getEntries()
        );

        $this->assertNotContains(
            [
                'line' => 30,
                'value' => 13,
            ],
            $fileReport->getEntries()
        );
    }

    /**
     * @param Extension[] $extensions
     *
     * @return Option
     */
    private function createOption(array $extensions = [])
    {
        $option = new Option;
        $option->setExtensions(
            array_merge(
                [
                    new ReturnExtension,
                    new ConditionExtension,
                    new SwitchCaseExtension
                ],
                $extensions
            )
        );

        return $option;
    }

    /**
     * @param Option        $option
     * @param HintList|null $hintList
     *
     * @return Detector
     */
    private function createDetector(Option $option, HintList $hintList = null)
    {
        if (null === $hintList) {
            $hintList = new HintList;
        }

        return new Detector($option, $hintList);
    }
}
