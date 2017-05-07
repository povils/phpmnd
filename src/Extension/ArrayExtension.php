<?php

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayItem;

/**
 * Class ArrayExtension
 *
 * @package Povils\PHPMND\Extension
 */
class ArrayExtension implements Extension
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'array';
    }

    /**
     * @inheritdoc
     */
    public function extend(Node $node)
    {
        return $node->getAttribute('parent') instanceof ArrayItem;
    }
}
