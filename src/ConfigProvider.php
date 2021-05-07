<?php

declare(strict_types=1);

namespace Dot\Cli;

use Dot\Cli\Factory\ApplicationFactory;
use Dot\Cli\Factory\FileLockerFactory;

/**
 * Class ConfigProvider
 * @package Dot\Cli
 */
class ConfigProvider
{
    /**
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    /**
     * @return string[][]
     */
    public function getDependencyConfig(): array
    {
        return [
            'aliases' => [
                FileLockerInterface::class => FileLocker::class
            ],
            'factories' => [
                Application::class => ApplicationFactory::class,
                FileLocker::class => FileLockerFactory::class
            ]
        ];
    }
}
