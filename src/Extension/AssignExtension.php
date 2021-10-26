<?php

declare(strict_types=1);

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use Povils\PHPMND\PhpParser\Visitor\ParentConnector;

class AssignExtension extends Extension
{
    public function getName(): string
    {
        return 'assign';
    }

    public function extend(Node $node): bool
    {
        return ParentConnector::findParent($node) instanceof Assign;
    }
}
