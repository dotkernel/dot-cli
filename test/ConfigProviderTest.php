<?php

declare(strict_types=1);

namespace DotTest\Cli;

use Dot\Cli\Application;
use Dot\Cli\ConfigProvider;
use Dot\Cli\FileLocker;
use Dot\Cli\FileLockerInterface;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    protected array $config;

    protected function setup(): void
    {
        $this->config = (new ConfigProvider())();
    }

    public function testHasDependencies(): void
    {
        $this->assertArrayHasKey('dependencies', $this->config);
    }

    public function testDependenciesHasFactories(): void
    {
        $this->assertArrayHasKey('factories', $this->config['dependencies']);
        $this->assertArrayHasKey(Application::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(FileLocker::class, $this->config['dependencies']['factories']);
    }

    public function testDependenciesHasAliases(): void
    {
        $this->assertArrayHasKey('aliases', $this->config['dependencies']);
        $this->assertArrayHasKey(FileLockerInterface::class, $this->config['dependencies']['aliases']);
    }
}
