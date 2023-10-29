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
    private CommandTester $commandTester;

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
        ]);

        $this->assertSame(RunCommand::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testExecuteWithHintOption(): void
    {
        $this->commandTester->execute([
            'directories' => ['tests/Fixtures/Files'],
            '--extensions' => 'assign',
            '--hint' => true,
        ]);

        $this->assertSame(RunCommand::FAILURE, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('Suggestions:', $this->commandTester->getDisplay());
    }

    public function testItDoesNotFailCommandWhenFileOnPathDoesNotExist(): void
    {
        $this->commandTester->execute([
            'directories' => ['tests/Fixtures/Files/FILE_DOES_NOT_EXIST.php'],
            '--extensions' => 'all',
        ]);

        $this->assertSame(RunCommand::SUCCESS, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('No files found to scan', $this->commandTester->getDisplay());
    }

    public function testFilterBySuffixes(): void
    {
        $this->commandTester->execute([
            'directories' => ['tests/Fixtures/Files'],
            '--extensions' => 'all',
            '--suffixes' => 'php5',
        ]);

        $this->assertSame(RunCommand::FAILURE, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('Total of Magic Numbers: 1', $this->commandTester->getDisplay());
    }
}
