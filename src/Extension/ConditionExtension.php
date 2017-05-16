<?php

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

/**
 * Class ConditionExtension
 *
 * @package Povils\PHPMND\Extension
 */
class ConditionExtension implements Extension
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'condition';
    }

    /**
     * @inheritdoc
     */
    public function extend(Node $node)
    {
        return
            $this->isCondition($node->getAttribute('parent'))
            &&
            false === $this->comparesToConst($node->getAttribute('parent'));
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function isCondition($node)
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

    /**
     * @param BinaryOp $node
     *
     * @return bool
     */
    private function comparesToConst($node)
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
