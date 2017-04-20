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

    /**
     * @inheritdoc
     */
    public function beforeTraverse(array $nodes)
    {
        $this->stack = [];
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node)
    {
        if (false === empty($this->stack)) {
            $node->setAttribute('parent', $this->stack[count($this->stack) - 1]);
        }
        $this->stack[] = $node;
    }

    /**
     * @inheritdoc
     */
    public function leaveNode(Node $node)
    {
        array_pop($this->stack);
    }
}
