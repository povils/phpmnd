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

    abstract public function extend(Node $node): bool;

    abstract public function getName(): string;

    public function setOption(Option $option)
    {
        $this->option = $option;
    }
}
