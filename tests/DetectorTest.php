<?php

declare(strict_types=1);

namespace Povils\PHPMND\Tests;

use Povils\PHPMND\HintList;
use function array_map;
use function iterator_to_array;
use Povils\PHPMND\Console\Option;
use Povils\PHPMND\DetectionResult;
use Povils\PHPMND\Detector;
use Povils\PHPMND\Extension\ArgumentExtension;
use Povils\PHPMND\Extension\ArrayExtension;
use Povils\PHPMND\Extension\AssignExtension;
use Povils\PHPMND\Extension\ConditionExtension;
use Povils\PHPMND\Extension\DefaultParameterExtension;
use Povils\PHPMND\Extension\OperationExtension;
use Povils\PHPMND\Extension\PropertyExtension;
use Povils\PHPMND\Extension\ReturnExtension;
use Povils\PHPMND\Extension\SwitchCaseExtension;
use Povils\PHPMND\PhpParser\FileParser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class DetectorTest extends TestCase
{
    private const FIXTURES_DIR = __DIR__ . '/Fixtures/Files';

    /**
     * @var Option
     */
    private $option;

    /**
     * @var Detector
     */
    private $detector;

    /**
     * @var HintList
     */
    private $hintList;

    protected function setUp(): void
    {
        $this->option = new Option();
        $this->option->setExtensions([
            new ReturnExtension(),
            new ConditionExtension(),
            new SwitchCaseExtension(),
        ]);

        $this->hintList = new HintList();

        $this->detector = new Detector(
            new FileParser((new ParserFactory())->create(ParserFactory::PREFER_PHP7)),
            $this->option,
            $this->hintList
        );
    }

    public function testDetectDefault(): void
    {
        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertSame(
            [
                [
                    'line' => 14,
                    'value' => 2,
                ],
                [
                    'line' => 15,
                    'value' => '0o15',
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
            $this->getActualResult($result)
        );
    }

    public function testDetectWithAssignExtension(): void
    {
        $this->option->setExtensions([new AssignExtension()]);
        $this->option->setIncludeNumericStrings(true);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertSame(
            [
                [
                    'line' => 5,
                    'value' => '4',
                ],
                [
                    'line' => 18,
                    'value' => 3,
                ],
            ],
            $this->getActualResult($result)
        );
    }

    public function testDetectWithPropertyExtension(): void
    {
        $this->option->setExtensions([new PropertyExtension()]);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertSame(
            [
                [
                    'line' => 11,
                    'value' => 6,
                ],
            ],
            $this->getActualResult($result)
        );
    }

    public function testDetectWithArrayExtension(): void
    {
        $this->option->setExtensions([new ArrayExtension()]);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertSame(
            [
                [
                    'line' => 30,
                    'value' => 13,
                ],
                [
                    'line' => 32,
                    'value' => 18,
                ],
                [
                    'line' => 33,
                    'value' => 123,
                ],
                [
                    'line' => 33,
                    'value' => 1234,
                ],
                [
                    'line' => 34,
                    'value' => 1234,
                ],
                [
                    'line' => 64,
                    'value' => 1234,
                ],
            ],
            $this->getActualResult($result)
        );
    }

    public function testDetectWithArgumentExtension(): void
    {
        $this->option->setExtensions([new ArgumentExtension()]);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertSame(
            [
                [
                    'line' => 3,
                    'value' => 3,
                ],
                [
                    'line' => 25,
                    'value' => 4,
                ],
            ],
            $this->getActualResult($result)
        );
    }

    public function testDetectWithDefaultParameterExtension(): void
    {
        $this->option->setExtensions([new DefaultParameterExtension()]);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertSame(
            [
                [
                    'line' => 13,
                    'value' => 4,
                ],
            ],
            $this->getActualResult($result)
        );
    }

    public function testDetectWithOperationExtension(): void
    {
        $this->option->setExtensions([new OperationExtension()]);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertSame(
            [
                [
                    'line' => 40,
                    'value' => 15,
                ],
                [
                    'line' => 43,
                    'value' => 20,
                ],
                [
                    'line' => 43,
                    'value' => 21,
                ],
            ],
            $this->getActualResult($result)
        );
    }

    public function testDetectWithIgnoreNumber(): void
    {
        $ignoreNumbers = [2, 10];
        $this->option->setIgnoreNumbers($ignoreNumbers);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        foreach ($this->getActualResult($result) as $entry) {
            $this->assertNotContains($entry['value'], $ignoreNumbers);
        }
    }

    public function testDetectWithIgnoreFunctions(): void
    {
        $this->option->setExtensions([new ArgumentExtension()]);
        $this->option->setIgnoreFuncs(['round']);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertNotContains(
            [
                'line' => 25,
                'value' => 4,
            ],
            $this->getActualResult($result)
        );
    }

    public function testDetectIncludeStrings(): void
    {
        $this->option->setIncludeStrings(true);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertContains(
            [
                'line' => 46,
                'value' => 'string',
            ],
            $this->getActualResult($result)
        );
    }

    public function testDetectIncludeStringsAndIgnoreString(): void
    {
        $this->option->setIncludeStrings(true);
        $this->option->setIgnoreStrings(['string']);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertNotContains(
            [
                'line' => 45,
                'value' => 'string',
            ],
            $this->getActualResult($result)
        );
    }

    public function testDonNotDetect0And1WithIncludeNumericStrings(): void
    {
        $this->option->setExtensions([new AssignExtension()]);
        $this->option->setIncludeNumericStrings(true);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_2.php'));

        $this->assertEmpty($this->getActualResult($result));
    }

    public function testDetectReadingNumber(): void
    {
        $this->option->setExtensions([new ArrayExtension()]);
        $this->option->setIncludeNumericStrings(true);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertContains(
            [
                'line' => 64,
                'value' => 1234,
            ],
            $this->getActualResult($result)
        );
    }

    public function testAllowArrayMappingWithArrayExtension(): void
    {
        $this->option->setExtensions([new ArrayExtension()]);
        $this->option->setAllowArrayMapping(true);
        $this->option->setIncludeNumericStrings(true);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $result = $this->getActualResult($result);

        $this->assertContains(
            [
                'line' => 32,
                'value' => 18,
            ],
            $result
        );

        $this->assertContains(
            [
                'line' => 33,
                'value' => 1234,
            ],
            $result
        );

        $this->assertContains(
            [
                'line' => 34,
                'value' => 1234,
            ],
            $result
        );

        $this->assertNotContains(
            [
                'line' => 30,
                'value' => 13,
            ],
            $result
        );
    }

    public function testDefaultIgnoreFunctions(): void
    {
        $this->option->setExtensions([new ArrayExtension()]);
        $this->option->setIncludeNumericStrings(true);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $results = $this->getActualResult($result);

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
        $this->option->setExtensions([new ArrayExtension()]);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_3.php'));

        $this->assertContains(
            [
                'line' => 4,
                'value' => 2,
            ],
            $this->getActualResult($result)
        );
    }

    public function testDetectWithHint(): void
    {
        $this->option->setExtensions([new AssignExtension]);
        $this->option->setGiveHint(true);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));
        $this->getActualResult($result);

        $this->assertTrue($this->hintList->hasHints());
        $this->assertSame(['TEST_1::TEST_1'], $this->hintList->getHintsByValue(3));
    }

    private function createSplFileInfo(string $filePath): SplFileInfo
    {
        return new SplFileInfo($filePath, '', '');
    }

    private function getActualResult(iterable $result): array
    {
        return array_map(
            static function (DetectionResult $detectionResult) {
                return [
                    'line' => $detectionResult->getLine(),
                    'value' => $detectionResult->getValue(),
                ];
            },
            iterator_to_array($result, false)
        );
    }

    public function testDetectDifferentBase(): void
    {


        $this->option->setExtensions([new AssignExtension()]);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_different_base.php'));
        $numbers = $this->getActualResult($result);
        $this->assertContains(
            [
                'line' => 5,
                'value' => '0x25',
            ],
            $numbers
        );

        $this->assertContains(
            [
                'line' => 6,
                'value' => '0b1111',
            ],
            $numbers
        );
    }

}
