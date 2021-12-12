<?php

declare(strict_types=1);

namespace Povils\PHPMND\PhpParser\Visitor;

use InvalidArgumentException;
use PhpParser\Node;

final class ParentConnector
{
    private const PARENT_ATTRIBUTE = 'parent';

    private function __construct()
    {
    }

    public static function setParent(Node $node, ?Node $parent): void
    {
        $node->setAttribute(self::PARENT_ATTRIBUTE, $parent);
    }

    public static function getParent(Node $node): Node
    {
        $parent = $node->getAttribute(self::PARENT_ATTRIBUTE);

        if ($parent === null) {
            throw new InvalidArgumentException('Expected that Node has parent Node.');
        }

        return $parent;
    }

    public static function findParent(Node $node): ?Node
    {
        return $node->getAttribute(self::PARENT_ATTRIBUTE);
    }
}
