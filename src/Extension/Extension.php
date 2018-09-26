<?php

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use Povils\PHPMND\Console\Option;

/**
 * @package Povils\PHPMND\Extension
 */
abstract class Extension
{
    /**
     * @var Option
     */
    protected $option;

    /**
     * @param Node $node
     *
     * @return bool
     */
    abstract public function extend(Node $node): bool;

    /**
     * @return string
     */
    abstract public function getName(): string;

    /**
     * @param Option $option
     */
    public function setOption(Option $option)
    {
        $this->option = $option;
    }
}
