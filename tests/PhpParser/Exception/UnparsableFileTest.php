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

        $this->assertSame(
            'Could not parse the file "/path/to/file". Check if it is a valid PHP file',
            $exception->getMessage()
        );

        $this->assertSame(0, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
