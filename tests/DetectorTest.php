<?php

namespace Povils\PHPMND\Tests;

use PHPUnit\Framework\TestCase;
use Povils\PHPMND\Console\Option;
use Povils\PHPMND\Detector;
use Povils\PHPMND\Extension\ArgumentExtension;
use Povils\PHPMND\Extension\ArrayExtension;
use Povils\PHPMND\Extension\AssignExtension;
use Povils\PHPMND\Extension\ConditionExtension;
use Povils\PHPMND\Extension\DefaultParameterExtension;
use Povils\PHPMND\Extension\Extension;
use Povils\PHPMND\Extension\OperationExtension;
use Povils\PHPMND\Extension\PropertyExtension;
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
    public function testDetectDefault(): void
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
                    'line' => 50,
                    'value' => -1,
                ],
            ],
            $fileReport->getEntries()
        );
    }

    public function testDetectWithAssignExtension(): void
    {
        $option = $this->createOption([new AssignExtension()]);
        $option->setIncludeNumericStrings(true);
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

    public function testDetectWithPropertyExtension(): void
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

    public function testDetectWithArrayExtension(): void
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

    public function testDetectWithArgumentExtension(): void
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

    public function testDetectWithDefaultParameterExtension(): void
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

    public function testDetectWithOperationExtension(): void
    {
        $option = $this->createOption([new OperationExtension()]);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 40,
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

    public function testDetectWithIgnoreNumber(): void
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

    public function testDetectWithIgnoreFuncs(): void
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

    public function testDetectIncludeStrings(): void
    {
        $option = $this->createOption();
        $option->setIncludeStrings(true);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 46,
                'value' => 'string',
            ],
            $fileReport->getEntries()
        );
    }

    public function testDetectIncludeStringsAndIgnoreString(): void
    {
        $option = $this->createOption();
        $option->setIncludeStrings(true);
        $option->setIgnoreStrings(['string']);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertNotContains(
            [
                'line' => 45,
                'value' => 'string',
            ],
            $fileReport->getEntries()
        );
    }

    public function testDetectWithHint(): void
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

    public function testDontDetect0And1WithIncludeNumericStrings(): void
    {
        $option = $this->createOption();
        $option->setExtensions([new AssignExtension]);
        $option->setIncludeNumericStrings(true);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_2'));

        $this->assertEmpty($fileReport->getEntries());
    }

    public function testDetectReadingNumber(): void
    {
        $option = $this->createOption();
        $option->setExtensions([new ArrayExtension]);
        $option->setIncludeNumericStrings(true);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 64,
                'value' => 1234,
            ],
            $fileReport->getEntries()
        );
    }

    public function testAllowArrayMappingWithArrayExtension(): void
    {
        $option = $this->createOption();
        $option->setExtensions([new ArrayExtension()]);
        $option->setAllowArrayMapping(true);
        $option->setIncludeNumericStrings(true);
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

        $this->assertContains(
            [
                'line' => 34,
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


    public function testDefaultIgnoreFunctions(): void
    {
        $option = $this->createOption();
        $option->setExtensions([new ArrayExtension()]);
        $option->setIncludeNumericStrings(true);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $results = $fileReport->getEntries();

        $this->assertNotContains(
            [
                'line' => 56,
                'value' => 13,
            ],
            $results
        );

        $this->assertNotContains(
            [
                'line' => 57,
                'value' => 3.14,
            ],
            $results
        );

        $this->assertNotContains(
            [
                'line' => 58,
                'value' => 10,
            ],
            $results
        );
    }

    public function testCheckForMagicArrayConstants(): void
    {
        $option = $this->createOption();
        $option->setExtensions([new ArrayExtension()]);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_3'));

        $this->assertContains(
            [
                'line' => 4,
                'value' => 2,
            ],
            $fileReport->getEntries()
        );
    }

    private function createOption(array $extensions = []): Option
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

    private function createDetector(Option $option, ?HintList $hintList = null): Detector
    {
        if (null === $hintList) {
            $hintList = new HintList;
        }

        return new Detector($option, $hintList);
    }
}
