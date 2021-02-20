<?php

declare(strict_types=1);

namespace Povils\PHPMND\Printer;

use Povils\PHPMND\FileReportList;
use Povils\PHPMND\HintList;
use Symfony\Component\Console\Output\OutputInterface;

interface Printer
{
    public function printData(OutputInterface $output, FileReportList $fileReportList, HintList $hintList): void;
}
