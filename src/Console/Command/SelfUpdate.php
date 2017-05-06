<?php

namespace Povils\PHPMND\Console\Command;

use Povils\PHPMND\Console\Application;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Humbug\SelfUpdate\Updater;
use Humbug\SelfUpdate\VersionParser;
use Humbug\SelfUpdate\Strategy\GithubStrategy;

class SelfUpdate extends BaseCommand
{

    /**
     * Packagist package name
     */
    const PACKAGE_NAME = 'povils/phpmnd';

    /**
     * This is the remote file name, not local name.
     */
    const FILE_NAME = 'phpmnd.phar';

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var string
     */
    protected $version;

    /**
     * Setup command and arguments.
     */
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setDescription('Update phpmnd.phar to most recent stable build.')
            ->addOption(
               'stable',
               's',
               InputOption::VALUE_NONE,
               'Update to most recent stable version of PHPMND tagged on Github.'
            )
            ->addOption(
               'rollback',
               'r',
               InputOption::VALUE_NONE,
               'Rollback to previous version of PHPMND if available on filesystem.'
            )
            ->addOption(
               'check',
               'c',
               InputOption::VALUE_NONE,
               'Checks what updates are available.'
            )
        ;
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->version = $this->getApplication()->getVersion();
        $parser = new VersionParser;

        /**
         * Check for ancilliary options
         */
        if ($input->getOption('rollback')) {
            $this->rollback();
            return;
        }

        if ($input->getOption('check')) {
            $this->printAvailableUpdates();
            return;
        }

        $this->updateToStableBuild();
    }

    /**
     * Perform update using phar-updater configured for stable versions.
     */
    protected function updateToStableBuild()
    {
        $this->update($this->getStableUpdater());
    }

    /**
     * Get phar-updater instance.
     */
    protected function getStableUpdater()
    {
        $updater = new Updater(null, false);
        $updater->setStrategy(Updater::STRATEGY_GITHUB);
        return $this->getGithubReleasesUpdater($updater);
    }

    /**
     * Perform in-place update of phar.
     */
    protected function update(Updater $updater)
    {
        $this->output->writeln('Updating...'.PHP_EOL);
        try {
            $result = $updater->update();

            $newVersion = $updater->getNewVersion();
            $oldVersion = $updater->getOldVersion();
        
            if ($result) {
                $this->output->writeln('<fg=green>PHPMND has been updated.</fg=green>');
                $this->output->writeln(sprintf(
                    '<fg=green>Current version is:</fg=green> <options=bold>%s</options=bold>.',
                    $newVersion
                ));
                $this->output->writeln(sprintf(
                    '<fg=green>Previous version was:</fg=green> <options=bold>%s</options=bold>.',
                    $oldVersion
                ));
            } else {
                $this->output->writeln('<fg=green>PHPMND is currently up to date.</fg=green>');
                $this->output->writeln(sprintf(
                    '<fg=green>Current version is:</fg=green> <options=bold>%s</options=bold>.',
                    $oldVersion
                ));
            }
        } catch (\Exception $e) {
            $this->output->writeln(sprintf('Error: <fg=yellow>%s</fg=yellow>', $e->getMessage()));
        }
        $this->output->write(PHP_EOL);
    }

    /**
     * Attempt to rollback to the previous phar version.
     */
    protected function rollback()
    {
        $updater = new Updater;
        try {
            $result = $updater->rollback();
            if ($result) {
                $this->output->writeln('<fg=green>PHPMND has been rolled back to prior version.</fg=green>');
            } else {
                $this->output->writeln('<fg=red>Rollback failed for reasons unknown.</fg=red>');
            }
        } catch (\Exception $e) {
            $this->output->writeln(sprintf('Error: <fg=yellow>%s</fg=yellow>', $e->getMessage()));
        }
    }

    protected function printAvailableUpdates()
    {
        $this->printCurrentLocalVersion();
        $this->printCurrentStableVersion();
    }

    /**
     * Print the current version of the phar in use.
     */
    protected function printCurrentLocalVersion()
    {
        $this->output->writeln(sprintf(
            'Your current local build version is: <options=bold>%s</options=bold>',
            $this->version
        ));
    }

    /**
     * Send updater to version printer.
     */
    protected function printCurrentStableVersion()
    {
        $this->printVersion($this->getStableUpdater());
    }

    /**
     * Print a remotely available version.
     * @param  Updater $updater
     */
    protected function printVersion(Updater $updater)
    {
        $stability = 'stable';
        try {
            if ($updater->hasUpdate()) {
                $this->output->writeln(sprintf(
                    'The current %s build available remotely is: <options=bold>%s</options=bold>',
                    $stability,
                    $updater->getNewVersion()
                ));
            } elseif (false == $updater->getNewVersion()) {
                $this->output->writeln(sprintf('There are no new %s builds available.', $stability));
            } else {
                $this->output->writeln(sprintf('You have the current %s build installed.', $stability));
            }
        } catch (\Exception $e) {
            $this->output->writeln(sprintf('Error: <fg=yellow>%s</fg=yellow>', $e->getMessage()));
        }
    }

    /**
     * Configure phar-updater with local phar details.
     * @param  Updater $updater
     * @return Updater
     */
    protected function getGithubReleasesUpdater(Updater $updater)
    {
        $updater->getStrategy()->setPackageName(self::PACKAGE_NAME);
        $updater->getStrategy()->setPharName(self::FILE_NAME);
        $updater->getStrategy()->setCurrentLocalVersion($this->version);
        return $updater;
    }
    
}
