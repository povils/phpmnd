<?php

declare(strict_types=1);

namespace Povils\PHPMND\Tests\PhpParser\Visitor;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;

abstract class BaseVisitorTest extends TestCase
{
    /**
     * @return Node[]
     */
    final protected function parseCode(string $code): array
    {
        return (array) (new ParserFactory())->create(ParserFactory::PREFER_PHP7)->parse($code);
    }

    /**
     * @param Node[] $nodes
     * @param NodeVisitor[] $visitors
     *
     * @return Node[]
     */
    final protected function traverse(array $nodes, array $visitors): array
    {
        $traverser = new NodeTraverser();

        foreach ($visitors as $visitor) {
            $traverser->addVisitor($visitor);
        }

        return $traverser->traverse($nodes);
    }
}
