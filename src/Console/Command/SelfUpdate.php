<?php

namespace Povils\PHPMND\Console\Command;

use Povils\PHPMND\Console\Application;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Humbug\SelfUpdate\Updater;
use Humbug\SelfUpdate\Strategy\GithubStrategy;

class SelfUpdate extends BaseCommand
{

    const REMOTE_FILENAME = 'phpmnd.phar';

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var string
     */
    private $version;

    /**
     * Setup command and arguments.
     */
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setDescription('Update phpmnd.phar to most recent stable build.')
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
                'Checks whether an update is available.'
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
    private function updateToStableBuild()
    {
        $this->update($this->getStableUpdater());
    }

    /**
     * Get phar-updater instance.
     */
    private function getStableUpdater()
    {
        $updater = new Updater(null, false);
        $updater->setStrategy(Updater::STRATEGY_GITHUB);
        return $this->getGithubReleasesUpdater($updater);
    }

    /**
     * Perform in-place update of phar.
     */
    private function update(Updater $updater)
    {
        $this->output->writeln('Updating...'.PHP_EOL);
        try {
            $result = $updater->update();

            $newVersion = $updater->getNewVersion();
            $oldVersion = $updater->getOldVersion();
        
            if ($result) {
                $this->output->writeln('<info>PHPMND has been updated.</info>');
                $this->output->writeln(sprintf(
                    '<info>Current version is:</info> <options=bold>%s</options=bold>.',
                    $newVersion
                ));
                $this->output->writeln(sprintf(
                    '<info>Previous version was:</info> <options=bold>%s</options=bold>.',
                    $oldVersion
                ));
            } else {
                $this->output->writeln('<info>PHPMND is currently up to date.</info>');
                $this->output->writeln(sprintf(
                    '<info>Current version is:</info> <options=bold>%s</options=bold>.',
                    $oldVersion
                ));
            }
        } catch (\Exception $e) {
            $this->output->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));
        }
        $this->output->write(PHP_EOL);
    }

    /**
     * Attempt to rollback to the previous phar version.
     */
    private function rollback()
    {
        $updater = new Updater(null, false);
        try {
            $result = $updater->rollback();
            if ($result) {
                $this->output->writeln('<info>PHPMND has been rolled back to prior version.</info>');
            } else {
                $this->output->writeln('<error>Rollback failed for reasons unknown.</error>');
            }
        } catch (\Exception $e) {
            $this->output->writeln(sprintf('Error: <error>%s</error>', $e->getMessage()));
        }
    }

    private function printAvailableUpdates()
    {
        $this->printCurrentLocalVersion();
        $this->printCurrentStableVersion();
    }

    /**
     * Print the current version of the phar in use.
     */
    private function printCurrentLocalVersion()
    {
        $this->output->writeln(sprintf(
            'Your current local build version is: <options=bold>%s</options=bold>',
            $this->version
        ));
    }

    /**
     * Send updater to version printer.
     */
    private function printCurrentStableVersion()
    {
        $this->printVersion($this->getStableUpdater());
    }

    /**
     * Print a remotely available version.
     * @param  Updater $updater
     */
    private function printVersion(Updater $updater)
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
            $this->output->writeln(sprintf('Error: <error>%s</error>', $e->getMessage()));
        }
    }

    /**
     * @param  Updater $updater
     * @return Updater
     */
    private function getGithubReleasesUpdater(Updater $updater)
    {
        $updater->getStrategy()->setPackageName(Application::PACKAGIST_PACKAGE_NAME);
        $updater->getStrategy()->setPharName(self::REMOTE_FILENAME);
        $updater->getStrategy()->setCurrentLocalVersion($this->version);
        return $updater;
    }
}
