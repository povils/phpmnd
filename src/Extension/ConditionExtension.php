<?php

declare(strict_types=1);

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Expr\BinaryOp\GreaterOrEqual;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\LogicalAnd;
use PhpParser\Node\Expr\BinaryOp\LogicalOr;
use PhpParser\Node\Expr\BinaryOp\LogicalXor;
use PhpParser\Node\Expr\BinaryOp\NotEqual;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\BinaryOp\Smaller;
use PhpParser\Node\Expr\BinaryOp\SmallerOrEqual;
use PhpParser\Node\Expr\BinaryOp\Spaceship;
use PhpParser\Node\Expr\ConstFetch;
use Povils\PHPMND\PhpParser\Visitor\ParentConnector;

class ConditionExtension extends Extension
{
    public function getName(): string
    {
        return 'condition';
    }

    public function extend(Node $node): bool
    {
        $parent = ParentConnector::findParent($node);

        return $parent && $this->isCondition($parent) && !$this->isComparesToConst($parent);
    }

    private function isCondition(Node $node): bool
    {
        return
            $node instanceof BinaryOp
            &&
            (
                $node instanceof Equal
                ||
                $node instanceof NotEqual
                ||
                $node instanceof Greater
                ||
                $node instanceof GreaterOrEqual
                ||
                $node instanceof Smaller
                ||
                $node instanceof SmallerOrEqual
                ||
                $node instanceof Identical
                ||
                $node instanceof NotIdentical
                ||
                $node instanceof LogicalAnd
                ||
                $node instanceof LogicalOr
                ||
                $node instanceof LogicalXor
                ||
                $node instanceof Coalesce
                ||
                $node instanceof Spaceship
            );
    }

    private function isComparesToConst(BinaryOp $node): bool
    {
        return
            $node instanceof BinaryOp
            &&
            (
                $node->left instanceof ConstFetch
                ||
                $node->right instanceof ConstFetch
            );
    }
}
