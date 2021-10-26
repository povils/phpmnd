<?php

declare(strict_types=1);

namespace Povils\PHPMND\Tests;

use Povils\PHPMND\DetectionResult;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class DetectionResultTest extends TestCase
{
    public function testItCreatesResult(): void
    {
        $file = new SplFileInfo(__DIR__ . '/Fixtures/Files/test_2.php', '', '');

        $line = 1;
        $value = 3;

        $result = new DetectionResult($file, $line, $value);

        $this->assertSame($file, $result->getFile());
        $this->assertSame($line, $result->getLine());
        $this->assertSame($value, $result->getValue());
    }
}
