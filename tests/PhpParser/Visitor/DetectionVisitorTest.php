<?php

declare(strict_types=1);

namespace Povils\PHPMND\Tests\PhpParser\Visitor;

use function iterator_to_array;
use Povils\PHPMND\DetectionResult;
use Povils\PHPMND\FileReportGenerator;
use Povils\PHPMND\PhpParser\Visitor\DetectionVisitor;

class DetectionVisitorTest extends BaseVisitorTest
{
    private const CODE = <<<'PHP'
<?php

class Foo {}

PHP;

    public function testItCollectsTheGeneratedDetections(): void
    {
        $detection0 = $this->createMock(DetectionResult::class);
        $detection1 = $this->createMock(DetectionResult::class);
        $detection2 = $this->createMock(DetectionResult::class);
        $detection3 = $this->createMock(DetectionResult::class);
        $detection4 = $this->createMock(DetectionResult::class);

        $fileReportGeneratorMock = $this->createMock(FileReportGenerator::class);

        $fileReportGeneratorMock->method('detect')
            ->willReturnOnConsecutiveCalls(
                [$detection0, $detection1],
                [$detection2],
                [$detection3, $detection4]
            );

        $visitor = new DetectionVisitor($fileReportGeneratorMock);

        $this->traverse(
            $this->parseCode(self::CODE),
            [$visitor]
        );

        $this->assertSame(
            [
                $detection0,
                $detection1,
                $detection2,
                // We only expect 2 calls here – because of the code parsed: hence even if the
                // generator can produce _more_ detections, we only call it as many times as we need
                // it, not as many times it can create detections
            ],
            iterator_to_array($visitor->getDetections(), false)
        );
    }

    public function testItResetsItsStateBetweenTwoTraverse(): void
    {
        $detection0 = $this->createMock(DetectionResult::class);
        $detection1 = $this->createMock(DetectionResult::class);
        $detection2 = $this->createMock(DetectionResult::class);
        $detection3 = $this->createMock(DetectionResult::class);
        $detection4 = $this->createMock(DetectionResult::class);

        $fileReportGeneratorMock = $this->createMock(FileReportGenerator::class);

        $fileReportGeneratorMock->method('detect')
            ->willReturnOnConsecutiveCalls(
                [$detection0, $detection1],
                [$detection2],
                [$detection3, $detection4],
                []
            );

        $visitor = new DetectionVisitor($fileReportGeneratorMock);

        $this->traverse(
            $this->parseCode(self::CODE),
            [$visitor]
        );

        // Sanity check
        $this->assertSame(
            [
                $detection0,
                $detection1,
                $detection2,
            ],
            iterator_to_array($visitor->getDetections(), false)
        );

        $this->traverse(
            $this->parseCode(self::CODE),
            [$visitor]
        );

        $this->assertSame(
            [
                $detection3,
                $detection4,
            ],
            iterator_to_array($visitor->getDetections(), false)
        );
    }
}
