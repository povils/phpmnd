<?php

declare(strict_types=1);

namespace Povils\PHPMND;

use Symfony\Component\Finder\SplFileInfo;

class DetectionResult
{
    private SplFileInfo $file;

    private int $line;

    /**
     * @var float|int|string
     */
    private $value;

    /**
     * @param string|int|float $value
     */
    public function __construct(SplFileInfo $file, int $line, $value)
    {
        $this->file = $file;
        $this->line = $line;
        $this->value = $value;
    }

    public function getFile(): SplFileInfo
    {
        return $this->file;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * @return float|int|string
     */
    public function getValue()
    {
        return $this->value;
    }
}
