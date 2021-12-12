<?php

declare(strict_types=1);

namespace Povils\PHPMND\Tests\Fixtures\PhpParser;

use Povils\PHPMND\PhpParser\Visitor\ParentConnector;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

final class ParentConnectorSpyVisitor extends NodeVisitorAbstract
{
    private $nodes;

    public function beforeTraverse(array $nodes): void
    {
        $this->nodes = [];
    }

    public function enterNode(Node $node): void
    {
        $this->nodes[] = ParentConnector::findParent($node);
    }

    public function leaveNode(Node $node): void
    {
    }

    /**
     * @return array<Node|null>
     */
    public function getCollectedNodes(): array
    {
        return $this->nodes;
    }
}
