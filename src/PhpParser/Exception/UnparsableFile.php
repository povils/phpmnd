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
                'Could not parse the file "%s". Check if it is a valid PHP file',
                $filePath
            ),
            0,
            $original
        );
    }
}
