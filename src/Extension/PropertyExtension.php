<?php

namespace PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Stmt\PropertyProperty;

/**
 * Class PropertyExtension
 *
 * @package PHPMND\Extension
 */
class PropertyExtension implements Extension
{
    /**
     * @inheritdoc
     */
    public function extend(Node $node)
    {
        return $node->getAttribute('parent') instanceof PropertyProperty;
    }
}
