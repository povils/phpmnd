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
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param int $line
     * @param int|float $value
     */
    public function addEntry($line, $value)
    {
        $this->entries[] = [
            'line' => $line,
            'value' => $value,
        ];
    }

    /**
     * @return array
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @return bool
     */
    public function hasMagicNumbers()
    {
        return false === empty($this->entries);
    }
}
