<?php

declare(strict_types=1);

namespace DotTest\Cli;

use Dot\Cli\Command\DemoCommand;
use Dot\Cli\FileLockerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

use function dirname;
use function getcwd;

/**
 * The shipped config is copied into applications, so it must load on every supported Symfony version.
 */
class ConfigTest extends TestCase
{
    public function testShippedConfigLoads(): void
    {
        $config = require dirname(__DIR__) . '/config/autoload/cli.global.php';

        $this->assertIsArray($config);
        $this->assertSame(['demo:command' => DemoCommand::class], $config['dot_cli']['commands']);
        $this->assertSame(
            ['enabled' => true, 'dirPath' => getcwd() . '/data/lock'],
            $config[FileLockerInterface::class]
        );
    }

    public function testShippedCommandKeyMatchesTheCommandName(): void
    {
        $config = require dirname(__DIR__) . '/config/autoload/cli.global.php';

        foreach ($config['dot_cli']['commands'] as $name => $class) {
            $command = new $class();
            $this->assertInstanceOf(Command::class, $command);
            $this->assertSame($name, $command->getName());
        }
    }
}
