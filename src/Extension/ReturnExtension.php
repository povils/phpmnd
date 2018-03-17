<?php

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Stmt\Return_;

class ReturnExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'return';
    }

    /**
     * @inheritdoc
     */
    public function extend(Node $node)
    {
        return $node->getAttribute('parent') instanceof Return_;
    }
}
