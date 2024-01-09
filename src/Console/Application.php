<?php

declare(strict_types=1);

namespace Povils\PHPMND\Console;

use Composer\InstalledVersions;
use OutOfBoundsException;
use Povils\PHPMND\Command\RunCommand;
use Povils\PHPMND\Container;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{
    public const PACKAGE_NAME = 'povils/phpmnd';

    private const NAME = 'phpmnd';

    private Container $container;

    public function __construct(Container $container)
    {
        parent::__construct(self::NAME, self::getPrettyVersion());

        $this->setDefaultCommand('run', true);

        $this->container = $container;
    }

    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        $hasVersionOption = $input->hasParameterOption(['--version', '-V'], true);

        if ($hasVersionOption === false) {
            $output->writeln($this->getLongVersion());
            $output->writeln('');
        }

        if ($hasVersionOption === false && $input->getFirstArgument() === null) {
            $input = new ArrayInput(['--help']);
        }

        return parent::doRun($input, $output);
    }

    public function getLongVersion(): string
    {
        return sprintf(
            '<info>%s</info> version <comment>%s</comment> by Povilas Susinskas',
            $this->getName(),
            $this->getVersion()
        );
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    protected function getDefaultCommands(): array
    {
        return [new HelpCommand(), new RunCommand()];
    }

    public static function getPrettyVersion(): string
    {
        // Pre 2.0 Composer runtime didn't have this class.
        if (!class_exists(InstalledVersions::class)) {
            return 'unknown';
        }

        try {
            return (string) InstalledVersions::getPrettyVersion(self::PACKAGE_NAME);
        } catch (OutOfBoundsException $e) {
            if (preg_match('#package .*' . preg_quote(self::PACKAGE_NAME, '#') . '.* not installed#i', $e->getMessage()) === 0) {
                throw $e;
            }

            // We have a bogus exception: how can PHPMND be not installed if we're here?
            return 'not-installed';
        }
    }
}
