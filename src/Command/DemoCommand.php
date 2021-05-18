<?php

declare(strict_types=1);

namespace Dot\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DemoCommand
 * @package Dot\Cli\Command
 */
class DemoCommand extends Command
{
    protected static $defaultName = 'demo:command';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Demo command description.')
            ->addOption('opt_required', 'r', InputOption::VALUE_REQUIRED, 'Required parameter')
            ->addOption('opt_optional', 'o', InputOption::VALUE_OPTIONAL, 'Optional parameter')
            ->addArgument('arg_required', InputArgument::REQUIRED, 'Required argument')
            ->addArgument('arg_optional', InputArgument::OPTIONAL, 'Optional argument', 'arg')
            ->addArgument('arg_array', InputArgument::IS_ARRAY, 'Array argument');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('opt_required=' . $input->getOption('opt_required'));
        $output->writeln('opt_optional=' . $input->getOption('opt_optional'));
        $output->writeln('arg_required=' . $input->getArgument('arg_required'));
        $output->writeln('arg_optional=' . $input->getArgument('arg_optional'));
        $output->writeln('arg_array=[' . implode(', ', $input->getArgument('arg_array')) . ']');

        return 0;
    }
}
