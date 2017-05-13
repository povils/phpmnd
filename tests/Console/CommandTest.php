<?php

namespace Povils\PHPMND\Tests\Console;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Povils\PHPMND\Console\Command;

/**
 * Class CommandTest
 *
 * @package Povils\PHPMND\Tests\Console
 */
class CommandTest extends TestCase
{

    public function testExecuteNoFilesFound()
    {
        $input = $this->createInput('bad_suffix');
        $output = $this->createOutput();

        $this->assertSame(Command::EXIT_CODE_SUCCESS, $this->execute([$input, $output]));
    }

    public function testExecuteWithViolationOption()
    {
        $input = $this->createInput(null, true);
        $output = $this->createOutput();

        $this->assertSame(Command::EXIT_CODE_FAILURE, $this->execute([$input, $output]));
    }

    /**
     * @param array $args
     *
     * @return int
     */
    private function execute(array $args)
    {
        $command = new Command;
        $class = new \ReflectionClass(new Command);
        $method = $class->getMethod('execute');
        $method->setAccessible(true);

        return $method->invokeArgs($command, $args);
    }

    /**
     * @param string $suffix
     * @param bool   $exitOnViolation
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function createInput($suffix = 'php', $exitOnViolation = false)
    {
        $input = $this->createMock('Symfony\Component\Console\Input\InputInterface');
        $input
            ->method('getOption')
            ->will(
                $this->returnValueMap(
                    [
                        ['exclude', []],
                        ['exclude-path', []],
                        ['exclude-file', []],
                        ['suffixes', $suffix],
                        ['non-zero-exit-on-violation', $exitOnViolation],
                    ]
                )
            );

        $input
            ->method('getArgument')
            ->will(
                $this->returnValueMap(
                    [
                        ['directory', 'tests/files'],
                    ]
                )
            );
        return $input;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function createOutput()
    {
        return $this->createMock('Symfony\Component\Console\Output\OutputInterface');
    }
}
