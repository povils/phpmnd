<?php

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Stmt\Return_;

class ReturnExtension extends Extension
{
    public function getName(): string
    {
        return 'return';
    }

    public function extend(Node $node): bool
    {
        return $node->getAttribute('parent') instanceof Return_;
    }
}
