<?php

namespace Povils\PHPMND\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Application
 *
 * @package Povils\PHPMND\Console
 */
class Application extends BaseApplication
{
    const VERSION = '2.2.0';
    const COMMAND_NAME = 'phpmnd';

    public function __construct()
    {
        parent::__construct('phpmnd', self::VERSION);
    }

    protected function getCommandName(InputInterface $input): string
    {
        return self::COMMAND_NAME;
    }

    protected function getDefaultCommands(): array
    {
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new Command;

        return $defaultCommands;
    }

    public function getDefinition(): InputDefinition
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();

        return $inputDefinition;
    }

    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        if (false === $input->hasParameterOption('--quiet')) {
            $output->write(
                sprintf(
                    'phpmnd %s by Povilas Susinskas' . PHP_EOL,
                    $this->getVersion()
                )
            );
        }

        if ($input->hasParameterOption('--version') || $input->hasParameterOption('-V')) {
            return Command::EXIT_CODE_SUCCESS;
        }

        if (null === $input->getFirstArgument()) {
            $input = new ArrayInput(['--help']);
        }

        return parent::doRun($input, $output);
    }
}
