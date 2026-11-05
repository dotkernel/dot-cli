<?php

declare(strict_types=1);

namespace DotTest\Cli\Command;

use Dot\Cli\Command\DemoCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class DemoCommandTest extends TestCase
{
    public function testNameAndDescriptionComeFromTheAttribute(): void
    {
        $command = new DemoCommand();

        $this->assertSame('demo:command', $command->getName());
        $this->assertSame('Demo command description.', $command->getDescription());
    }

    public function testCommandWillExecute(): void
    {
        $tester = new CommandTester(new DemoCommand());

        $this->assertSame(Command::SUCCESS, $tester->execute([]));
        $this->assertSame('', $tester->getDisplay());
    }
}
