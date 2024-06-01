<?php

declare(strict_types=1);

namespace Povils\PHPMND\Printer;

use InvalidArgumentException;
use Povils\PHPMND\HintList;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Laurent Laville
 * @credits inspired by PHPMD
 *          (https://github.com/phpmd/phpmd/blob/2.15.0/src/main/php/PHPMD/TextUI/CommandLineOptions.php#L780))
 */
class Custom implements Printer
{
    private Printer $innerPrinter;

    public function __construct(string $reportFormat)
    {
        if (!class_exists($reportFormat)) {
            // Try to load a custom resource
            $fileName = strtr($reportFormat, '_\\', '//') . '.php';

            $fileHandle = @fopen($fileName, 'r', true);
            if (is_resource($fileHandle) === false) {
                throw new InvalidArgumentException(
                    sprintf(
                        "Can't find the custom report class: %s",
                        $reportFormat
                    )
                );
            }
            @fclose($fileHandle);

            include_once $fileName;
        }

        $printer = new $reportFormat();
        if (!$printer instanceof Printer) {
            throw new InvalidArgumentException(
                sprintf(
                    'Custom report class "%s" does not implement "%s" contract',
                    get_class($printer),
                    Printer::class
                )
            );
        }
        $this->innerPrinter = $printer;
    }

    public function printData(OutputInterface $output, HintList $hintList, array $detections): void
    {
        $this->innerPrinter->printData($output, $hintList, $detections);
    }
}
