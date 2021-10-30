<?php

namespace Povils\PHPMND\Printer;

class Plain implements Decorator
{

    public function getLine(string $fileContents, int $lineNumber): string
    {
        $format = '  > %d| %s';
        return sprintf(
            $format,
            $lineNumber,
            explode("\n", $fileContents)[$lineNumber - 1]
        );
    }
}
