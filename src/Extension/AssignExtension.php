<?php

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;

class AssignExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'assign';
    }

    /**
     * @inheritdoc
     */
    public function extend(Node $node): bool
    {
        return $node->getAttribute('parent') instanceof Assign;
    }
}
