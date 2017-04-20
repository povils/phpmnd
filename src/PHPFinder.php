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
    public function __construct()
    {
        parent::__construct();
        $this
            ->files()
            ->name('*.php')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true);
    }
}
