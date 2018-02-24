<?php

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar\String_;

/**
 * Class ArrayMappingExtension
 *
 * @package Povils\PHPMND\Extension
 */
class ArrayMappingExtension implements Extension
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'array_mapping';
    }

    /**
     * @inheritdoc
     */
    public function extend(Node $node)
    {
        $parent = $node->getAttribute('parent');

        return 
            $parent instanceof ArrayItem &&
            !($parent->key instanceof String_);
        ;
    }
}
