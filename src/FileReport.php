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

    /**
     * @return SplFileInfo
     */
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

    /**
     * @return array
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * @return bool
     */
    public function hasMagicNumbers(): bool
    {
        return false === empty($this->entries);
    }
}
