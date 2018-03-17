<?php

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Param;

class DefaultParameterExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'default_parameter';
    }

    /**
     * @inheritdoc
     */
    public function extend(Node $node)
    {
        return $node->getAttribute('parent') instanceof Param;
    }
}
