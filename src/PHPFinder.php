<?php

namespace PHPMND;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Finder\Finder;

/**
 * Class Finder
 *
 * @package PHPMND
 */
class PHPFinder extends Finder
{
    /**
     * @param InputInterface $input
     */
    public function __construct(InputInterface $input)
    {
        parent::__construct();
        $this
            ->files()
            ->in($input->getArgument('directory'))
            ->exclude(array_merge(['vendor'], $input->getOption('exclude')))
            ->name('*.php')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true);

        foreach ($input->getOption('exclude-path') as $notPath) {
            $this->notPath($notPath);
        }

        foreach ($input->getOption('exclude-file') as $notName) {
            $this->notName($notName);
        }
    }
}
