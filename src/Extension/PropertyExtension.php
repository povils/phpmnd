<?php

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Stmt\PropertyProperty;

class PropertyExtension extends Extension
{
    public function getName(): string
    {
        return 'property';
    }

    public function extend(Node $node): bool
    {
        return $node->getAttribute('parent') instanceof PropertyProperty;
    }
}
