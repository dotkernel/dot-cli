<?php

declare(strict_types=1);

namespace Dot\Cli;

use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Class Application
 * @package Dot\Cli
 */
class Application extends \Symfony\Component\Console\Application
{
    private FileLockerInterface $fileLocker;

    /**
     * Application constructor.
     * @param FileLockerInterface $fileLocker
     * @param array $config
     */
    public function __construct(FileLockerInterface $fileLocker, array $config)
    {
        parent::__construct($config['name'] ?? 'UNKNOWN', $config['version'] ?? 'UNKNOWN');

        $this->fileLocker = $fileLocker;
    }

    /**
     * Application destructor.
     */
    public function __destruct()
    {
        $this->fileLocker->unlock();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Throwable
     */
    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        $commandName = $this->getCommandName($input);
        $this->fileLocker->setCommandName($commandName)->setEnabled(true);

        try {
            $this->fileLocker->lock();
        } catch (Exception $exception) {
            $output->writeln($exception->getMessage());
            return 0;
        }

        return parent::doRun($input, $output);
    }
}
