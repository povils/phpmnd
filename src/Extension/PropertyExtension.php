<?php

declare(strict_types=1);

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Stmt\PropertyProperty;
use Povils\PHPMND\PhpParser\Visitor\ParentConnector;

class PropertyExtension extends Extension
{
    public function getName(): string
    {
        return 'property';
    }

    public function extend(Node $node): bool
    {
        return ParentConnector::findParent($node) instanceof PropertyProperty;
    }
}
