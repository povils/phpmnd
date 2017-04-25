<?php

namespace Povils\PHPMND\Console;

use Povils\PHPMND\Extension\DefaultExtension;
use Povils\PHPMND\Extension\Extension;

/**
 * @package Povils\PHPMND\Console
 */
class Option
{
    /**
     * @var Extension[]
     */
    private $extensions;

    /**
     * @var array
     */
    private $ignoreNumbers = [0, 0., 1];

    /**
     * @var array
     */
    private $ignoreFuncs = [];

    public function __construct()
    {
        $this->extensions[] = new DefaultExtension();
    }

    /**
     * @param Extension $extension
     */
    public function addExtension(Extension $extension)
    {
        $this->extensions[] = $extension;
    }

    /**
     * @return Extension[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * @param array $ignoreNumbers
     */
    public function setIgnoreNumbers(array $ignoreNumbers)
    {
        $this->ignoreNumbers = array_merge($this->ignoreNumbers, $ignoreNumbers);
    }

    /**
     * @return array
     */
    public function getIgnoreNumbers()
    {
        return $this->ignoreNumbers;
    }

    /**
     * @param array $ignoreFuncs
     */
    public function setIgnoreFuncs(array $ignoreFuncs)
    {
        $this->ignoreFuncs = $ignoreFuncs;
    }

    /**
     * @return array
     */
    public function getIgnoreFuncs()
    {
        return $this->ignoreFuncs;
    }
}
