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
    public function getHintsByValue($magicNumber)
    {
        $hints = [];
        foreach ($this->constants as $constant) {
            if ($constant['value'] === $magicNumber) {
                $hints[] = $constant['hint'];
            }
        }

        return $hints;
    }

    /**
     * @return bool
     */
    public function hasHints()
    {
        return false === empty($this->constants);
    }

    /**
     * @param mixed  $value
     * @param string $className
     * @param string $constName
     */
    public function addClassCont($value, $className, $constName)
    {
        $this->constants[] = [
            'value' => $value,
            'hint' => sprintf('%s::%s', $className, $constName)
        ];
    }
}
