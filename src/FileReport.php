<?php

namespace Povils\PHPMND;

use Symfony\Component\Finder\SplFileInfo;

/**
 * @package Povils\PHPMND
 */
class FileReport
{
    /**
     * @var array
     */
    private $entries = [];

    /**
     * @var SplFileInfo
     */
    private $file;

    /**
     * @param SplFileInfo $file
     */
    public function __construct(SplFileInfo $file)
    {
        $this->file = $file;
    }

    public function getFile(): SplFileInfo
    {
        return $this->file;
    }

    /**
     * @param int $line
     * @param int|float $value
     */
    public function addEntry(int $line, $value): void
    {
        $this->entries[] = [
            'line' => $line,
            'value' => $value,
        ];
    }

    public function getEntries(): array
    {
        return $this->entries;
    }

    public function hasMagicNumbers(): bool
    {
        return false === empty($this->entries);
    }
}
