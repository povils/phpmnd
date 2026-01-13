<?php

declare(strict_types=1);

namespace Povils\PHPMND\Tests\PhpParser\Exception;

use Exception;
use Povils\PHPMND\PhpParser\Exception\UnparsableFile;
use PHPUnit\Framework\TestCase;

class UnparsableFileTest extends TestCase
{
    public function testItCanCreateUserFriendlyErrorForGivenFile(): void
    {
        $previous = new Exception('Unintentional thing');

        $exception = UnparsableFile::fromInvalidFile('/path/to/file', $previous);

        $this->assertStringContainsString('Could not parse the file "/path/to/file"', $exception->getMessage());
        $this->assertStringContainsString('Unintentional thing', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testFromSyntaxErrorIncludesLineNumber(): void
    {
        $exception = UnparsableFile::fromSyntaxError('/path/to/file.php', 42, 'unexpected token');

        $this->assertStringContainsString('Syntax error', $exception->getMessage());
        $this->assertStringContainsString('line 42', $exception->getMessage());
        $this->assertStringContainsString('unexpected token', $exception->getMessage());
    }

    public function testFileNotFoundIndicatesFileDoesNotExist(): void
    {
        $exception = UnparsableFile::fileNotFound('/missing/file.php');

        $this->assertStringContainsString('Could not find file', $exception->getMessage());
        $this->assertStringContainsString('/missing/file.php', $exception->getMessage());
    }
}
