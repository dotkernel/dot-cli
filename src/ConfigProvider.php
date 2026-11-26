<?php

declare(strict_types=1);

namespace Dot\Cli;

use Dot\Cli\Factory\ApplicationFactory;
use Dot\Cli\Factory\FileLockerFactory;

class ConfigProvider
{
    /**
     * @return array{dependencies: array<string, array<string, string>>}
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function getDependencyConfig(): array
    {
        return [
            'aliases'   => [
                FileLockerInterface::class => FileLocker::class,
            ],
            'factories' => [
                Application::class => ApplicationFactory::class,
                FileLocker::class  => FileLockerFactory::class,
            ],
        ];
    }
}
