<?php

namespace PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;

/**
 * Class AssignExtension
 *
 * @package PHPMND\Extension
 */
class AssignExtension implements Extension
{
    /**
     * @inheritdoc
     */
    public function extend(Node $node)
    {
        return $node->getAttribute('parent') instanceof Assign;
    }
}
