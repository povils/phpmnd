<?php

declare(strict_types=1);

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Param;
use Povils\PHPMND\PhpParser\Visitor\ParentConnector;

class DefaultParameterExtension extends Extension
{
    public function getName(): string
    {
        return 'default_parameter';
    }

    public function extend(Node $node): bool
    {
        return ParentConnector::findParent($node) instanceof Param;
    }
}
