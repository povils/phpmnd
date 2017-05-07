<?php

namespace Povils\PHPMND\Tests;

use PHPUnit\Framework\TestCase;
use Povils\PHPMND\Extension\ReturnExtension;
use Povils\PHPMND\ExtensionResolver;

/**
 * Class ExtensionResolverTest
 *
 * @package Povils\PHPMND\Tests
 */
class ExtensionResolverTest extends TestCase
{
    public function testResolveDefault()
    {
        $resolver = $this->createResolver();
        $extensions = $resolver->resolve([]);

        $this->assertSame($resolver->defaults(), $extensions);
    }

    public function testResolveAll()
    {
        $resolver = $this->createResolver();
        $extensions = $resolver->resolve(['all']);
        
        $this->assertSame($resolver->all(), $extensions);
    }

    public function testResolveWithMinus()
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

    public function testResolveNotExistingExtension()
    {
        $this->expectException(\InvalidArgumentException::class);

        $resolver = $this->createResolver();
        $resolver->resolve(['not_existing']);
    }

    /**
     * @return ExtensionResolver
     */
    private function createResolver()
    {
        return new ExtensionResolver();
    }
}
