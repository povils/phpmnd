<?php

namespace Povils\PHPMND;

/**
 * Class HintList
 *
 * @package Povils\PHPMND
 */
class HintList
{
    /**
     * @var array
     */
    private $constants = [];

    /**
     * @param mixed $magicNumber
     *
     * @return array
     */
    public function getHintsByValue($magicNumber): array
    {
        $hints = [];
        foreach ($this->constants as $constant) {
            if ($constant['value'] === $magicNumber) {
                $hints[] = $constant['hint'];
            }
        }

        return $hints;
    }

    public function hasHints(): bool
    {
        return false === empty($this->constants);
    }

    /**
     * @param mixed  $value
     * @param string $className
     * @param string $constName
     */
    public function addClassCont($value, string $className, string $constName): void
    {
        $this->constants[] = [
            'value' => $value,
            'hint' => sprintf('%s::%s', $className, $constName)
        ];
    }
}
