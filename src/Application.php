<?php

declare(strict_types=1);

namespace Dot\Cli;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use function is_string;

class Application extends \Symfony\Component\Console\Application
{
    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly FileLockerInterface $fileLocker,
        array $config,
    ) {
        $name    = $config['name'] ?? null;
        $version = $config['version'] ?? null;

        parent::__construct(
            is_string($name) ? $name : 'UNKNOWN',
            is_string($version) ? $version : 'UNKNOWN'
        );
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
        $this->fileLocker->setCommandName($this->resolveCommandName($input));

        try {
            $this->fileLocker->lock();
        } catch (Exception $exception) {
            $errorOutput = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
            $errorOutput->writeln(
                '<error>' . $exception->getMessage() . '</error>',
                OutputInterface::VERBOSITY_QUIET
            );

            return Command::FAILURE;
        }

        return parent::doRun($input, $output);
    }

    /**
     * Resolve abbreviations and aliases to the real command name, so that every spelling of a command shares
     * one lock. Unknown and ambiguous names are not locked: parent::doRun() reports them.
     */
    private function resolveCommandName(InputInterface $input): ?string
    {
        $name = $this->getCommandName($input);
        if ($name === null) {
            return null;
        }

        try {
            return $this->find($name)->getName();
        } catch (ExceptionInterface) {
            return null;
        }
    }
}
