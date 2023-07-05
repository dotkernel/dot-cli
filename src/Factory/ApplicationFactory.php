<?php

declare(strict_types=1);

namespace Dot\Cli\Factory;

use Dot\Cli\FileLockerInterface;
use Exception;
use Laminas\Cli\ContainerCommandLoader;
use Laminas\Cli\Listener\TerminateListener;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;

use function array_key_exists;
use function is_array;

class ApplicationFactory
{
    public const MESSAGE_MISSING_CONFIG          = 'Unable to find config.';
    public const MESSAGE_MISSING_PACKAGE_CONFIG  = 'Unable to find dot-cli config.';
    public const MESSAGE_MISSING_CONFIG_COMMANDS = 'Unable to find dot-cli config: commands.';

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container): Application
    {
        if (! $container->has('config')) {
            throw new Exception(self::MESSAGE_MISSING_CONFIG);
        }
        $config = $container->get('config');

        if (
            ! array_key_exists('dot_cli', $config)
            || ! is_array($config['dot_cli'])
            || empty($config['dot_cli'])
        ) {
            throw new Exception(self::MESSAGE_MISSING_PACKAGE_CONFIG);
        }
        $config = $config['dot_cli'];

        if (
            ! array_key_exists('commands', $config)
            || ! is_array($config['commands'])
            || empty($config['commands'])
        ) {
            throw new Exception(self::MESSAGE_MISSING_CONFIG_COMMANDS);
        }

        $eventDispatcherServiceName = __NAMESPACE__ . '\SymfonyEventDispatcher';
        $dispatcher                 = $container->has($eventDispatcherServiceName)
            ? $container->get($eventDispatcherServiceName)
            : new EventDispatcher();
        $dispatcher->addListener(ConsoleEvents::TERMINATE, new TerminateListener($config));

        if (! $container->has(FileLockerInterface::class)) {
            throw new Exception(FileLockerFactory::MESSAGE_MISSING_FILE_LOCKER);
        }

        $application = new \Dot\Cli\Application(
            $container->get(FileLockerInterface::class),
            $config
        );
        $application->setCommandLoader(new ContainerCommandLoader($container, $config['commands']));
        $application->setDispatcher($dispatcher);
        $application->setAutoExit(false);

        return $application;
    }
}
