<?php

namespace Povils\PHPMND\Console;

use Povils\PHPMND\Extension\Extension;

/**
 * @package Povils\PHPMND\Console
 */
class Option
{
    /**
     * @var Extension[]
     */
    private $extensions = [];

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
    private $ignoreStrings = ['', '0', '1'];

    /**
     * @var bool
     */
    private $includeStrings = false;

    /**
     * @var bool
     */
    private $giveHint = false;

    /**
     * @var bool
     */
    private $includeNumericStrings = false;

    /**
     * @var bool
     */
    private $allowArrayMapping = false;

    /**
     * @param Extension[] $extensions
     */
    public function setExtensions(array $extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * @return Extension[]
     */
    public function getExtensions(): array
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
    public function getIgnoreNumbers(): array
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
    public function getIgnoreFuncs(): array
    {
        return $this->ignoreFuncs;
    }

    /**
     * @return bool
     */
    public function includeStrings(): ?bool
    {
        return $this->includeStrings;
    }

    /**
     * @param bool $includeStrings
     */
    public function setIncludeStrings(?bool $includeStrings)
    {
        $this->includeStrings = $includeStrings;
    }

    /**
     * @return array
     */
    public function getIgnoreStrings(): array
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

    /**
     * @return boolean
     */
    public function giveHint(): bool
    {
        return $this->giveHint;
    }

    /**
     * @param boolean $giveHint
     */
    public function setGiveHint(bool $giveHint)
    {
        $this->giveHint = $giveHint;
    }

    /**
     * @return bool
     */
    public function includeNumericStrings(): ?bool
    {
        return $this->includeNumericStrings;
    }

    /**
     * @param bool $includeNumericStrings
     */
    public function setIncludeNumericStrings(?bool $includeNumericStrings)
    {
        $this->includeNumericStrings = $includeNumericStrings;
    }

    /**
     * @return bool
     */
    public function allowArrayMapping(): ?bool
    {
        return $this->allowArrayMapping;
    }

    /**
     * @param bool $allowArrayMapping
     */
    public function setAllowArrayMapping(?bool $allowArrayMapping)
    {
        $this->allowArrayMapping = $allowArrayMapping;
    }
}
