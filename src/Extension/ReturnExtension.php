<?php

declare(strict_types=1);

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Stmt\Return_;
use Povils\PHPMND\PhpParser\Visitor\ParentConnector;

class ReturnExtension extends Extension
{
    public function getName(): string
    {
        return 'return';
    }

    public function extend(Node $node): bool
    {
        return ParentConnector::findParent($node) instanceof Return_;
    }
}
