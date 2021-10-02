<?php

declare(strict_types=1);

namespace Povils\PHPMND\Console;

use Povils\PHPMND\Command\RunCommand;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{
    public const VERSION = '2.4.0';
    private const NAME = 'phpmnd';

    public function __construct()
    {
        parent::__construct(self::NAME, self::VERSION);

        $this->setDefaultCommand('run', true);
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
        return trim(sprintf(
            '<info>%s</info> version <comment>%s</comment> by Povilas Susinskas',
            $this->getName(),
            $this->getVersion()
        ));
    }

    protected function getDefaultCommands(): array
    {
        return [new HelpCommand(), new RunCommand()];
    }
}
