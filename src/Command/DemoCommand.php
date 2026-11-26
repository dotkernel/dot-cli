<?php

declare(strict_types=1);

namespace Dot\Cli\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: DemoCommand::DEFAULT_NAME,
    description: 'Demo command description.',
)]
class DemoCommand extends Command
{
    public const string DEFAULT_NAME = 'demo:command';

    protected function configure(): void
    {
        $this
            ->setName(self::DEFAULT_NAME)
            ->setDescription('Demo command description.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return Command::SUCCESS;
    }
}
