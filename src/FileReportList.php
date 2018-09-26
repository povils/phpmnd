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

    /**
     * @param FileReport $fileReport
     */
    public function addFileReport(FileReport $fileReport): void
    {
        $this->fileReports[] = $fileReport;
    }

    /**
     * @return FileReport[]
     */
    public function getFileReports(): array
    {
        return $this->fileReports;
    }

    /**
     * @return bool
     */
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
