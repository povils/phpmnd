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
    /**
     * @param string $directory
     * @param array  $exclude
     * @param array  $excludePaths
     * @param array  $excludeFiles
     * @param array  $suffixes
     */
    public function __construct(
        $directory,
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
