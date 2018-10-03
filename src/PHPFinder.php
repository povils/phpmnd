<?php

namespace Povils\PHPMND;

use Symfony\Component\Finder\Finder;

/**
 * Class Finder
 *
 * @package Povils\PHPMND
 */
class PHPFinder extends Finder
{
    public function __construct(
        string $directory,
        array $exclude,
        array $excludePaths,
        array $excludeFiles,
        array $suffixes
    ) {
        parent::__construct();
        $this
            ->files()
            ->in($directory)
            ->exclude(array_merge(['vendor'], $exclude))
            ->ignoreDotFiles(true)
            ->ignoreVCS(true);

        foreach ($suffixes as $suffix) {
            $this->name('*.' . $suffix);
        }

        foreach ($excludePaths as $notPath) {
            $this->notPath($notPath);
        }

        foreach ($excludeFiles as $notName) {
            $this->notName($notName);
        }
    }
}
