<?php

namespace PHPMND\Console;

use PHPMND\Extension\DefaultExtension;
use PHPMND\Extension\Extension;

/**
 * @package PHPMND\Console
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

    /**
     * @var array
     */
    private $ignoreStrings = [''];

    /**
     * @var bool
     */
    private $includeStrings = false;

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

    /**
     * @return bool
     */
    public function includeStrings()
    {
        return $this->includeStrings;
    }

    /**
     * @param bool $includeStrings
     */
    public function setIncludeStrings($includeStrings)
    {
        $this->includeStrings = $includeStrings;
    }

    /**
     * @return array
     */
    public function getIgnoreStrings()
    {
        return $this->ignoreStrings;
    }

    /**
     * @param array $ignoreStrings
     */
    public function setIgnoreStrings(array $ignoreStrings)
    {
        $this->ignoreStrings = array_merge($this->ignoreStrings, $ignoreStrings);
    }
}
