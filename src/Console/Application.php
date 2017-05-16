<?php

namespace Povils\PHPMND\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Application
 *
 * @package Povils\PHPMND\Console
 */
class Application extends BaseApplication
{
    const VERSION = '1.1.1';
    const COMMAND_NAME = 'phpmnd';

    public function __construct()
    {
        parent::__construct('phpmnd', self::VERSION);
    }

    /**
     * @inheritdoc
     */
    protected function getCommandName(InputInterface $input)
    {
        return self::COMMAND_NAME;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new Command;

        return $defaultCommands;
    }

    /**
     * @inheritdoc
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();

        return $inputDefinition;
    }

    /**
     * @inheritdoc
     */
    public function doRun(InputInterface $input, OutputInterface $output)
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
            exit;
        }

        if (null === $input->getFirstArgument()) {
            $input = new ArrayInput(['--help']);
        }

        return parent::doRun($input, $output);
    }
}
