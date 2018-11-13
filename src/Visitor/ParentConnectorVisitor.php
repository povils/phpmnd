<?php

namespace Povils\PHPMND\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ParentConnectorVisitor extends NodeVisitorAbstract
{
    /**
     * @var array
     */
    private $stack;

    public function beforeTraverse(array $nodes): void
    {
        $this->stack = [];
    }

    public function enterNode(Node $node): void
    {
        if (false === empty($this->stack)) {
            $node->setAttribute('parent', $this->stack[count($this->stack) - 1]);
        }
        $this->stack[] = $node;
    }

    public function leaveNode(Node $node): void
    {
        array_pop($this->stack);
    }
}
