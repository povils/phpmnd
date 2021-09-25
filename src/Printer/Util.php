<?php

namespace Povils\PHPMND\Printer;

/**
 * @internal
 */
abstract class Util
{

    /**
     * Get the snippet and information about it
     *
     * @param string $content
     * @param int $line
     * @param int|string $text
     * @return array{snippet: string, line: int, magic: int|string, col: false|int}
     */
    final public static function getSnippet(string $content, int $line, $text): array
    {
        $content = str_replace(array("\r\n", "\r"), "\n", $content);
        $lines = explode("\n", $content);

        $lineContent = array_slice($lines, $line-1, 1);
        $lineContent = reset($lineContent);
        $start = strpos($lineContent, $text.'');

        return [
            'snippet' => $lineContent,
            'line' => $line,
            'magic' => $text,
            'col' => $start
        ];
    }
}
