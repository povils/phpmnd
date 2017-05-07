<?php

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Div;
use PhpParser\Node\Expr\BinaryOp\Minus;
use PhpParser\Node\Expr\BinaryOp\Mod;
use PhpParser\Node\Expr\BinaryOp\Mul;
use PhpParser\Node\Expr\BinaryOp\Plus;
use PhpParser\Node\Expr\BinaryOp\Pow;
use PhpParser\Node\Expr\BinaryOp\ShiftLeft;
use PhpParser\Node\Expr\BinaryOp\ShiftRight;

/**
 * Class OperationExtension
 *
 * @package Povils\PHPMND\Extension
 */
class OperationExtension implements Extension
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'operation';
    }

    /**
     * @inheritdoc
     */
    public function extend(Node $node)
    {
        $parentNode = $node->getAttribute('parent');

        return
            $parentNode instanceof Mul
            ||
            $parentNode instanceof Div
            ||
            $parentNode instanceof Plus
            ||
            $parentNode instanceof Minus
            ||
            $parentNode instanceof Mod
            ||
            $parentNode instanceof ShiftLeft
            ||
            $parentNode instanceof ShiftRight
            ||
            $parentNode instanceof Pow;
    }
}
