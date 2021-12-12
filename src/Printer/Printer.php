<?php

declare(strict_types=1);

namespace Povils\PHPMND\Printer;

use Povils\PHPMND\HintList;
use Symfony\Component\Console\Output\OutputInterface;

interface Printer
{
    public function printData(OutputInterface $output, HintList $hintList, array $detections): void;
}
