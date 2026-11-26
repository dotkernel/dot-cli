<?php

declare(strict_types=1);

namespace DotTest\Cli;

use Dot\Cli\Application;
use Dot\Cli\ConfigProvider;
use Dot\Cli\Factory\ApplicationFactory;
use Dot\Cli\Factory\FileLockerFactory;
use Dot\Cli\FileLocker;
use Dot\Cli\FileLockerInterface;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    /** @var array{dependencies: array<string, array<string, string>>} */
    protected array $config;

    protected function setUp(): void
    {
        $this->config = (new ConfigProvider())();
    }

    public function testHasDependencies(): void
    {
        $this->assertArrayHasKey('dependencies', $this->config);
        $this->assertSame((new ConfigProvider())->getDependencyConfig(), $this->config['dependencies']);
    }

    public function testDependenciesHasFactories(): void
    {
        $this->assertSame(
            [
                Application::class => ApplicationFactory::class,
                FileLocker::class  => FileLockerFactory::class,
            ],
            $this->config['dependencies']['factories']
        );
    }

    public function testDependenciesHasAliases(): void
    {
        $this->assertSame(
            [FileLockerInterface::class => FileLocker::class],
            $this->config['dependencies']['aliases']
        );
    }

    public function testRegistersNoAbstractFactories(): void
    {
        $this->assertArrayNotHasKey('abstract_factories', $this->config['dependencies']);
    }
}
