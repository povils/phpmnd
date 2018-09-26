<?php

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Param;

class DefaultParameterExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'default_parameter';
    }

    /**
     * @inheritdoc
     */
    public function extend(Node $node): bool
    {
        return $node->getAttribute('parent') instanceof Param;
    }
}
