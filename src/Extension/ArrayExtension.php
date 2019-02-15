<?php

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Scalar\String_;

class ArrayExtension extends Extension
{
    public function getName(): string
    {
        return 'array';
    }

    public function extend(Node $node): bool
    {
        $parent = $node->getAttribute('parent');

        return (
            $parent instanceof ArrayItem  &&
            false === $this->ignoreArray($parent)
          ) || $parent instanceof ArrayDimFetch;
    }

    private function ignoreArray(ArrayItem $node): bool
    {
        $arrayKey = $node->key;
        
        return $this->option->allowArrayMapping() &&
        $arrayKey instanceof String_ &&
        false === ($this->option->includeNumericStrings() && is_numeric($arrayKey->value));
    }
}
