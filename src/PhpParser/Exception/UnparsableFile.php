<?php

declare(strict_types=1);

namespace Povils\PHPMND\PhpParser\Exception;

use RuntimeException;
use function sprintf;
use Throwable;

final class UnparsableFile extends RuntimeException
{
    public static function fromInvalidFile(string $filePath, Throwable $original): self
    {
        return new self(
            sprintf(
                'Could not parse the file "%s". %s',
                $filePath,
                $original->getMessage()
            ),
            0,
            $original
        );
    }

    public static function fromSyntaxError(string $filePath, int $line, string $error): self
    {
        return new self(
            sprintf(
                'Syntax error in file "%s" at line %d: %s',
                $filePath,
                $line,
                $error
            )
        );
    }

    public static function fileNotFound(string $filePath): self
    {
        return new self(
            sprintf(
                'Could not find file "%s". Verify the file path exists and is readable',
                $filePath
            )
        );
    }
}
