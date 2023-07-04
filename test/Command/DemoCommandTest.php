<?php

declare(strict_types=1);

namespace DotTest\Cli\Command;

use Dot\Cli\Command\DemoCommand;
use DotTest\Cli\CommonTrait;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

class DemoCommandTest extends TestCase
{
    use CommonTrait;

    public function testWillCreateCommand(): void
    {
        $command = new DemoCommand();
        $this->assertInstanceOf(DemoCommand::class, $command);
    }

    /**
     * @throws ReflectionException
     */
    public function testCommandWillExecute(): void
    {
        $command    = new DemoCommand();
        $reflection = new ReflectionMethod(DemoCommand::class, 'execute');

        $result = $reflection->invoke(
            $command,
            new ArgvInput(),
            new BufferedOutput()
        );
        $this->assertSame($result, Command::SUCCESS);
    }
}
