<?php

declare(strict_types=1);

namespace Dot\Cli\Factory;

use Dot\Cli\FileLocker;
use Dot\Cli\FileLockerInterface;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

/**
 * Class FileLockerFactory
 * @package Dot\Cli\Factory
 */
class FileLockerFactory
{
    /**
     * @param ContainerInterface $container
     * @return FileLockerInterface
     */
    public function __invoke(ContainerInterface $container): FileLockerInterface
    {
        $config = $container->get('config')[FileLockerInterface::class] ?? [];
        Assert::isMap($config);

        $dirPath = $config['dirPath'];
        Assert::stringNotEmpty($dirPath);

        $fileLocker = new FileLocker();
        $fileLocker->setEnabled($config['enabled'] ?? false)->setDirPath($dirPath);

        return $fileLocker;
    }
}
