<?php

declare(strict_types=1);

namespace Povils\PHPMND\Tests\Command;

use PHPUnit\Framework\TestCase;
use Povils\PHPMND\Command\RunCommand;
use Povils\PHPMND\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class RunCommandTest extends TestCase
{
    /**
     * @var CommandTester
     */
    private $commandTester;

    protected function setUp(): void
    {
        $application = new Application();
        $command = new RunCommand();
        $application->add($command);

        $this->commandTester = new CommandTester($application->find($command->getName()));
    }

    public function testExecuteNoFilesFound(): void
    {
        $this->commandTester->execute([
            'directories' => ['tests/Fixtures'],
            '--suffixes' => 'bad_suffix',
        ]);

        $this->assertSame(RunCommand::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testExecuteWithViolationOption(): void
    {
        $this->commandTester->execute([
            'directories' => ['tests/Fixtures'],
            '--non-zero-exit-on-violation' => true,
        ]);

        $this->assertSame(RunCommand::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testExecuteWithHintOption(): void
    {
        $this->commandTester->execute([
            'directories' => ['tests/Fixtures'],
            '--extensions' => 'assign',
            '--non-zero-exit-on-violation' => true,
            '--hint' => true,
        ]);

        $this->assertSame(RunCommand::FAILURE, $this->commandTester->getStatusCode());
        $this->assertTrue(strpos($this->commandTester->getDisplay(), 'Suggestions:') !== false);
    }
}
