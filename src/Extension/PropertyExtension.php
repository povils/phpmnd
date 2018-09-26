<?php

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Stmt\PropertyProperty;

class PropertyExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'property';
    }

    /**
     * @inheritdoc
     */
    public function extend(Node $node): bool
    {
        return $node->getAttribute('parent') instanceof PropertyProperty;
    }
}
