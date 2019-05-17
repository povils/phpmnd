<?php

namespace Povils\PHPMND\Console;

use Povils\PHPMND\Extension\Extension;
use Povils\PHPMND\GenericLanguage;
use Povils\PHPMND\Language;

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
    private $ignoreFuncs = [
        'intval',
        'floatval',
        'strval',
    ];

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
     * @var array
     */
    private $checkNaming = [];

    public function setExtensions(array $extensions)
    {
        $this->extensions = $extensions;
    }

    public function getExtensions(): array
    {
        return $this->extensions;
    }

    public function setIgnoreNumbers(array $ignoreNumbers)
    {
        $this->ignoreNumbers = array_merge($this->ignoreNumbers, $ignoreNumbers);
    }

    public function getIgnoreNumbers(): array
    {
        return $this->ignoreNumbers;
    }

    public function setIgnoreFuncs(array $ignoreFuncs)
    {
        $this->ignoreFuncs = $ignoreFuncs;
    }

    public function getIgnoreFuncs(): array
    {
        return $this->ignoreFuncs;
    }

    public function includeStrings(): ?bool
    {
        return $this->includeStrings;
    }

    public function setIncludeStrings(?bool $includeStrings)
    {
        $this->includeStrings = $includeStrings;
    }

    public function getIgnoreStrings(): array
    {
        return $this->ignoreStrings;
    }

    public function setIgnoreStrings(array $ignoreStrings)
    {
        $this->ignoreStrings = array_merge($this->ignoreStrings, $ignoreStrings);
    }

    public function giveHint(): bool
    {
        return $this->giveHint;
    }

    public function setGiveHint(bool $giveHint)
    {
        $this->giveHint = $giveHint;
    }

    public function includeNumericStrings(): ?bool
    {
        return $this->includeNumericStrings;
    }

    public function setIncludeNumericStrings(?bool $includeNumericStrings)
    {
        $this->includeNumericStrings = $includeNumericStrings;
    }

    public function allowArrayMapping(): ?bool
    {
        return $this->allowArrayMapping;
    }

    public function setAllowArrayMapping(?bool $allowArrayMapping)
    {
        $this->allowArrayMapping = $allowArrayMapping;
    }

    /**
     * @return Language[]
     */
    public function checkNaming(): array
    {
        return $this->checkNaming;
    }

    public function setCheckNaming(array $checkNaming)
    {
        $languages = [];
        foreach ($checkNaming as $language) {
            $languages[] = $this->findLanguage($language);
        }
        $this->checkNaming = $languages;
    }

    protected function findLanguage($language): Language
    {
        foreach (scandir(__DIR__ . '/../Languages/') as $class) {
            $class = basename($class, '.php');
            $class = "Povils\PHPMND\Languages\\$class";
            if ((
                class_exists($class) &&
                is_subclass_of($class, Language::class) &&
                in_array($language, $class::providesLanguages())
            )) {
                return new $class($language);
            }
        }

        return new GenericLanguage($language);
    }
}
