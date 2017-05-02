<?php

namespace PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Param;

/**
 * Class DefaultParameterExtension
 *
 * @package PHPMND\Extension
 */
class DefaultParameterExtension implements Extension
{
    /**
     * @inheritdoc
     */
    public function extend(Node $node)
    {
        return $node->getAttribute('parent') instanceof Param;
    }
}
