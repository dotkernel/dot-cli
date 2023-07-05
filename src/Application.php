<?php

declare(strict_types=1);

namespace Dot\Cli;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class Application extends \Symfony\Component\Console\Application
{
    private FileLockerInterface $fileLocker;

    public function __construct(FileLockerInterface $fileLocker, array $config)
    {
        parent::__construct($config['name'] ?? 'UNKNOWN', $config['version'] ?? 'UNKNOWN');

        $this->fileLocker = $fileLocker;
    }

    public function __destruct()
    {
        $this->fileLocker->unlock();
    }

    /**
     * @throws Throwable
     */
    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        $this->fileLocker->setCommandName($this->getCommandName($input));

        try {
            $this->fileLocker->lock();
        } catch (Exception $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }

        return parent::doRun($input, $output);
    }
}
