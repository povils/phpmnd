<?php

declare(strict_types=1);

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
use Povils\PHPMND\PhpParser\Visitor\ParentConnector;

class OperationExtension extends Extension
{
    public function getName(): string
    {
        return 'operation';
    }

    public function extend(Node $node): bool
    {
        $parentNode = ParentConnector::findParent($node);

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
