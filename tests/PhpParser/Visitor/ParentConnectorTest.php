<?php

declare(strict_types=1);

namespace Povils\PHPMND\Tests\PhpParser\Visitor;

use InvalidArgumentException;
use Povils\PHPMND\PhpParser\Visitor\ParentConnector;
use PhpParser\Node\Stmt\Nop;
use PHPUnit\Framework\TestCase;

class ParentConnectorTest extends TestCase
{
    public function testItCanProvideTheNodeParent(): void
    {
        $parent = new Nop();

        $node = new Nop(['parent' => $parent]);

        $this->assertSame($parent, ParentConnector::getParent($node));
        $this->assertSame($parent, ParentConnector::findParent($node));
    }

    public function testItCanLookForTheNodeParent(): void
    {
        $parent = new Nop();

        $node1 = new Nop(['parent' => $parent]);
        $node2 = new Nop(['parent' => null]);
        $node3 = new Nop();

        $this->assertSame($parent, ParentConnector::findParent($node1));
        $this->assertNull(ParentConnector::findParent($node2));
        $this->assertNull(ParentConnector::findParent($node3));
    }

    public function testItCannotProvideTheNodeParentIfHasNotBeenSetYet(): void
    {
        $node = new Nop();

        $this->expectException(InvalidArgumentException::class);

        // We are not interested in a more helpful message here since it would be the result of
        // a misconfiguration on our part rather than a user one. Plus this would require some
        // extra processing on a part which is quite a hot path.

        ParentConnector::getParent($node);
    }

    public function testItCanSetNodeParent(): void
    {
        $parent = new Nop();
        $node = new Nop();

        ParentConnector::setParent($node, $parent);

        $this->assertSame($parent, ParentConnector::getParent($node));

        ParentConnector::setParent($node, null);

        $this->assertNull(ParentConnector::findParent($node));
    }
}
