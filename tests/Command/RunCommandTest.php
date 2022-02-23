<?php

declare(strict_types=1);

namespace Povils\PHPMND\Tests\Command;

use PHPUnit\Framework\TestCase;
use Povils\PHPMND\Command\RunCommand;
use Povils\PHPMND\Console\Application;
use Povils\PHPMND\Container;
use Symfony\Component\Console\Tester\CommandTester;

class RunCommandTest extends TestCase
{
    /**
     * @var CommandTester
     */
    private $commandTester;

    protected function setUp(): void
    {
        $application = new Application(Container::create());
        $command = new RunCommand();
        $application->add($command);

        $this->commandTester = new CommandTester($application->find($command->getName()));
    }

    public function testExecuteNoFilesFound(): void
    {
        $this->commandTester->execute([
            'directories' => ['tests/Fixtures/Files'],
            '--suffixes' => 'bad_suffix',
        ]);

        $this->assertSame(RunCommand::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testExecuteWithViolationOption(): void
    {
        $this->commandTester->execute([
            'directories' => ['tests/Fixtures/Files'],
            '--non-zero-exit-on-violation' => true,
        ]);

        $this->assertSame(RunCommand::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testExecuteWithHintOption(): void
    {
        $this->commandTester->execute([
            'directories' => ['tests/Fixtures/Files'],
            '--extensions' => 'assign',
            '--non-zero-exit-on-violation' => true,
            '--hint' => true,
        ]);

        $this->assertSame(RunCommand::FAILURE, $this->commandTester->getStatusCode());
        $this->assertTrue(strpos($this->commandTester->getDisplay(), 'Suggestions:') !== false);
    }

    public function testItDoesNotFailCommandWhenFileOnPathDoesNotExist(): void
    {
        $this->commandTester->execute([
            'directories' => ['tests/Fixtures/Files/FILE_DOES_NOT_EXIST.php'],
            '--extensions' => 'all',
        ]);

        $this->assertSame(RunCommand::SUCCESS, $this->commandTester->getStatusCode());
        $output = $this->commandTester->getDisplay();

        /* This should use assertStringContainsString but the lowest phpunit supported does not allow that */
        $found = false;
        if (strpos($output, 'No files found to scan') !== false) {
            $found = true;
        }

        $this->assertTrue($found);
    }
}
