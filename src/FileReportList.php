<?php

namespace Povils\PHPMND;

/**
 * Class FileReportList
 *
 * @package Povils\PHPMND
 */
class FileReportList
{
    /**
     * @var FileReport[]
     */
    private $fileReports = [];

    public function addFileReport(FileReport $fileReport): void
    {
        $this->fileReports[] = $fileReport;
    }

    public function getFileReports(): array
    {
        return $this->fileReports;
    }

    public function hasMagicNumbers(): bool
    {
        foreach ($this->fileReports as $fileReport) {
            if ($fileReport->hasMagicNumbers()) {
                return true;
            }
        }

        return false;
    }
}
