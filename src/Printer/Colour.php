<?php

namespace Povils\PHPMND\Printer;

use JakubOnderka\PhpConsoleColor\ConsoleColor;
use JakubOnderka\PhpConsoleHighlighter\Highlighter;

class Colour implements Decorator
{
    private $highlighter;

    public function __construct()
    {
        $this->highlighter = new Highlighter(new ConsoleColor());
    }

    public function getLine(string $fileContents, int $lineNumber): string
    {
        return $this->highlighter->getCodeSnippet($fileContents, $lineNumber, 0, 0);
    }
}
