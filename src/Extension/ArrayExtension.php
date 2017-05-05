<?php

namespace PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayItem;

/**
 * Class ArrayExtension
 *
 * @package PHPMND\Extension
 */
class ArrayExtension implements Extension
{
    /**
     * @inheritdoc
     */
    public function extend(Node $node)
    {
        return $node->getAttribute('parent') instanceof ArrayItem;
    }
}
