<?php

declare(strict_types=1);

namespace Povils\PHPMND;

class HintList
{
    private array $constants = [];

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
        return $this->constants !== [];
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
