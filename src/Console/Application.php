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
    const PACKAGIST_PACKAGE_NAME = 'povils/phpmnd';

    public function __construct()
    {
        parent::__construct(self::COMMAND_NAME, self::VERSION);
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

        if ('run' === (string) $input) {
            $input = new ArrayInput(['run','--help']);
        }

        return parent::doRun($input, $output);
    }
}
