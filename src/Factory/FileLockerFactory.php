<?php

declare(strict_types=1);

namespace Dot\Cli\Factory;

use Dot\Cli\Exception\InvalidConfigException;
use Dot\Cli\FileLocker;
use Dot\Cli\FileLockerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function array_key_exists;
use function is_array;
use function is_bool;
use function is_string;

class FileLockerFactory
{
    public const string MESSAGE_MISSING_FILE_LOCKER        = 'Unable to find FileLocker service.';
    public const string MESSAGE_MISSING_FILE_LOCKER_CONFIG = 'Missing/invalid dot-cli FileLocker config.';

    public const string MESSAGE_MISSING_FILE_LOCKER_CONFIG_ENABLED
        = 'Missing/invalid dot-cli FileLocker config: enabled';

    public const string MESSAGE_MISSING_FILE_LOCKER_CONFIG_DIR_PATH
        = 'Missing/invalid dot-cli FileLocker config: dirPath';

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidConfigException
     */
    public function __invoke(ContainerInterface $container): FileLockerInterface
    {
        if (! $container->has('config')) {
            throw new InvalidConfigException(ApplicationFactory::MESSAGE_MISSING_CONFIG);
        }
        $config = $container->get('config');

        if (
            ! is_array($config)
            || ! array_key_exists(FileLockerInterface::class, $config)
            || ! is_array($config[FileLockerInterface::class])
            || empty($config[FileLockerInterface::class])
        ) {
            throw new InvalidConfigException(self::MESSAGE_MISSING_FILE_LOCKER_CONFIG);
        }
        $config = $config[FileLockerInterface::class];

        if (
            ! array_key_exists('enabled', $config)
            || ! is_bool($config['enabled'])
        ) {
            throw new InvalidConfigException(self::MESSAGE_MISSING_FILE_LOCKER_CONFIG_ENABLED);
        }

        if (
            ! array_key_exists('dirPath', $config)
            || ! is_string($config['dirPath'])
            || empty($config['dirPath'])
        ) {
            throw new InvalidConfigException(self::MESSAGE_MISSING_FILE_LOCKER_CONFIG_DIR_PATH);
        }

        return (new FileLocker())
            ->setEnabled($config['enabled'])
            ->setDirPath($config['dirPath']);
    }
}
