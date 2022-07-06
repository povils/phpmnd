<?php

declare(strict_types=1);

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use Povils\PHPMND\Console\Option;

abstract class Extension
{
    protected Option $option;

    abstract public function extend(Node $node): bool;

    abstract public function getName(): string;

    public function setOption(Option $option)
    {
        $this->option = $option;
    }
}
