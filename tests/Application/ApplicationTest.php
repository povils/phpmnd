<?php

declare(strict_types=1);

namespace Povils\PHPMND\Tests\Application;

use PHPUnit\Framework\TestCase;
use Povils\PHPMND\Console\Application;
use Povils\PHPMND\Container;
use SebastianBergmann\Version;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * @author Laurent Laville
 * @group tags
 *   Allow to exclude this group from CI workflow when tag is not pushed
 */
class ApplicationTest extends TestCase
{
    private ApplicationTester $applicationTester;
    protected function setUp(): void
    {
        $application = new Application(Container::create());
        $application->setAutoExit(false);

        $this->applicationTester = new ApplicationTester($application);
    }

    public function testApplicationVersionInstalled(): void
    {
        $this->applicationTester->run(['--version', '--no-ansi']);

        $installedVersion = \ltrim(
            (new Version('', dirname(__DIR__, 2)))->getVersion(),
            '-v'
        );

        $this->assertSame(
            \sprintf('phpmnd version %s by Povilas Susinskas', $installedVersion),
            \trim($this->applicationTester->getDisplay())
        );
    }
}
