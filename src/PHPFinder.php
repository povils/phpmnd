<?php

declare(strict_types=1);

namespace Povils\PHPMND;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class PHPFinder extends Finder
{
    public function __construct(
        array $directories,
        array $exclude,
        array $excludePaths,
        array $excludeFiles,
        array $suffixes
    ) {
        parent::__construct();
        $dirs = array_filter($directories, 'is_dir');
        $files = array_filter($directories, 'is_file');

        $this
            ->files()
            ->in($dirs)
            ->exclude(array_merge(['vendor'], $exclude))
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->append(
                array_map(
                    function (string $file) {
                        return new SplFileInfo(realpath($file), dirname($file), $file);
                    },
                    $files
                )
            );

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
