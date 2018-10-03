<?php

namespace Povils\PHPMND\Printer;

use Povils\PHPMND\FileReportList;
use Povils\PHPMND\HintList;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface Printer
 *
 * @package Povils\PHPMND\Printer
 */
interface Printer
{
    public function printData(OutputInterface $output, FileReportList $fileReportList, HintList $hintList): void;
}
