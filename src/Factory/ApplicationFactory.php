<?php

declare(strict_types=1);

namespace Dot\Cli\Factory;

use Dot\Cli\Application;
use Dot\Cli\Exception\InvalidConfigException;
use Dot\Cli\FileLockerInterface;
use Laminas\Cli\ContainerCommandLoader;
use Laminas\Cli\Listener\TerminateListener;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use function array_key_exists;
use function is_array;

class ApplicationFactory
{
    public const string MESSAGE_MISSING_CONFIG          = 'Unable to find config.';
    public const string MESSAGE_MISSING_PACKAGE_CONFIG  = 'Unable to find dot-cli config.';
    public const string MESSAGE_MISSING_CONFIG_COMMANDS = 'Unable to find dot-cli config: commands.';

    /**
     * Service key of a custom Symfony event dispatcher. It is the key laminas-cli uses.
     */
    public const string EVENT_DISPATCHER = 'Laminas\Cli\SymfonyEventDispatcher';

    /**
     * @deprecated 4.0.0 Register the dispatcher under self::EVENT_DISPATCHER. This key will be removed in 5.0.0.
     */
    public const string LEGACY_EVENT_DISPATCHER = 'Dot\Cli\Factory\SymfonyEventDispatcher';

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidConfigException
     */
    public function __invoke(ContainerInterface $container): Application
    {
        if (! $container->has('config')) {
            throw new InvalidConfigException(self::MESSAGE_MISSING_CONFIG);
        }
        $config = $container->get('config');

        if (
            ! is_array($config)
            || ! array_key_exists('dot_cli', $config)
            || ! is_array($config['dot_cli'])
            || empty($config['dot_cli'])
        ) {
            throw new InvalidConfigException(self::MESSAGE_MISSING_PACKAGE_CONFIG);
        }
        $config = $config['dot_cli'];

        if (
            ! array_key_exists('commands', $config)
            || ! is_array($config['commands'])
            || empty($config['commands'])
        ) {
            throw new InvalidConfigException(self::MESSAGE_MISSING_CONFIG_COMMANDS);
        }

        $dispatcher = $this->getEventDispatcher($container);
        $dispatcher->addListener(ConsoleEvents::TERMINATE, new TerminateListener($config));

        if (! $container->has(FileLockerInterface::class)) {
            throw new InvalidConfigException(FileLockerFactory::MESSAGE_MISSING_FILE_LOCKER);
        }
        $fileLocker = $container->get(FileLockerInterface::class);
        if (! $fileLocker instanceof FileLockerInterface) {
            throw new InvalidConfigException(FileLockerFactory::MESSAGE_MISSING_FILE_LOCKER);
        }

        $application = new Application($fileLocker, $config);
        $application->setCommandLoader(new ContainerCommandLoader($container, $config['commands']));
        $application->setDispatcher($dispatcher);
        $application->setAutoExit(false);

        return $application;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidConfigException
     */
    private function getEventDispatcher(ContainerInterface $container): EventDispatcherInterface
    {
        foreach ([self::EVENT_DISPATCHER, self::LEGACY_EVENT_DISPATCHER] as $serviceName) {
            if (! $container->has($serviceName)) {
                continue;
            }

            $dispatcher = $container->get($serviceName);
            if (! $dispatcher instanceof EventDispatcherInterface) {
                throw InvalidConfigException::invalidEventDispatcher();
            }

            return $dispatcher;
        }

        return new EventDispatcher();
    }
}
