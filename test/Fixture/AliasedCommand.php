<?php

declare(strict_types=1);

namespace DotTest\Cli\Fixture;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'fixture:command', description: 'Fixture command.', aliases: ['fc'])]
final class AliasedCommand extends Command
{
    public const string OUTPUT = 'fixture command ran';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(self::OUTPUT);

        return Command::SUCCESS;
    }
}
