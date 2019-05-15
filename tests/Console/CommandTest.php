<?php

namespace Povils\PHPMND\Tests\Console;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Povils\PHPMND\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CommandTest
 *
 * @package Povils\PHPMND\Tests\Console
 */
class CommandTest extends TestCase
{

    public function testExecuteNoFilesFound(): void
    {
        $input = $this->createInput(null, 'bad_suffix');
        $output = $this->createOutput();

        $this->assertSame(Command::EXIT_CODE_SUCCESS, $this->execute([$input, $output]));
    }

    public function testExecuteWithViolationOption(): void
    {
        $input = $this->createInput(null, null, true);
        $output = $this->createOutput();

        $this->assertSame(Command::EXIT_CODE_FAILURE, $this->execute([$input, $output]));
    }

    public function testExecuteWithHintOption(): void
    {
        $input = $this->createInput('assign', null, true, true);
        $output = $this->createOutput();
        $output
            ->expects($this->at(9))
            ->method('writeln')
            ->with('Suggestions:');

        $this->execute([$input, $output]);
    }

    private function execute(array $args): int
    {
        $command = new Command;
        $class = new \ReflectionClass(new Command);
        $method = $class->getMethod('execute');
        $method->setAccessible(true);

        return $method->invokeArgs($command, $args);
    }

    protected function createInput(
        ?string $extensions = '',
        ?string $suffix = 'php',
        bool $exitOnViolation = false,
        bool $hint = false
    ): MockObject {
        $input = $this->createMock(InputInterface::class);
        $input
            ->method('getOption')
            ->will(
                $this->returnValueMap(
                    [
                        ['extensions', $extensions],
                        ['exclude', []],
                        ['exclude-path', []],
                        ['exclude-file', []],
                        ['suffixes', $suffix],
                        ['non-zero-exit-on-violation', $exitOnViolation],
                        ['hint', $hint],
                    ]
                )
            );

        $input
            ->method('getArgument')
            ->will(
                $this->returnValueMap(
                    [
                        ['directories', ['tests/files']],
                    ]
                )
            );
        return $input;
    }

    protected function createOutput(): MockObject
    {
        return $this->createMock(OutputInterface::class);
    }
}
