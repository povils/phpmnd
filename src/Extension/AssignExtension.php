<?php

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;

class AssignExtension extends Extension
{
    public function getName(): string
    {
        return 'assign';
    }

    public function extend(Node $node): bool
    {
        return $node->getAttribute('parent') instanceof Assign;
    }
}
