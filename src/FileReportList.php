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
    public function addFileReport(FileReport $fileReport)
    {
        $this->fileReports[] = $fileReport;
    }

    /**
     * @return FileReport[]
     */
    public function getFileReports()
    {
        return $this->fileReports;
    }

    /**
     * @return bool
     */
    public function hasMagicNumbers()
    {
        foreach ($this->fileReports as $fileReport) {
            if ($fileReport->hasMagicNumbers()) {
                return true;
            }
        }

        return false;
    }
}
