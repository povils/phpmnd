<?php
declare(strict_types=1);
namespace Povils\PHPMND\Printer;

interface Decorator
{
    public function getLine(string $fileContents, int $lineNumber): string;
}
