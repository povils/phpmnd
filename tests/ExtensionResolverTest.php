<?php

declare(strict_types=1);

namespace Povils\PHPMND\Tests;

use PHPUnit\Framework\TestCase;
use Povils\PHPMND\Extension\AssignExtension;
use Povils\PHPMND\Extension\ReturnExtension;
use Povils\PHPMND\ExtensionResolver;

class ExtensionResolverTest extends TestCase
{
    public function testResolveDefault(): void
    {
        $resolver = $this->createResolver();
        $extensions = $resolver->resolve([]);

        $this->assertSame($resolver->defaults(), $extensions);
    }

    public function testResolveAddExtension(): void
    {
        $resolver = $this->createResolver();
        $extensions = $resolver->resolve(['assign']);

        foreach ($extensions as $extension) {
            if (get_class($extension) === AssignExtension::class) {
                $this->assertTrue(true);
                return;
            }
        }

        $this->assertTrue(false);
    }

    public function testResolveAll(): void
    {
        $resolver = $this->createResolver();
        $extensions = $resolver->resolve(['all']);

        $this->assertSame($resolver->all(), $extensions);
    }

    public function testResolveWithMinus(): void
    {
        $resolver = $this->createResolver();
        $extensions = $resolver->resolve(['-return']);

        foreach ($extensions as $extension) {
            if (get_class($extension) === ReturnExtension::class) {
                $this->assertTrue(false);
            }
        }

        $this->assertTrue(true);
    }

    public function testResolveNotExistingExtension(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $resolver = $this->createResolver();
        $resolver->resolve(['not_existing']);
    }

    private function createResolver(): ExtensionResolver
    {
        return new ExtensionResolver();
    }
}
