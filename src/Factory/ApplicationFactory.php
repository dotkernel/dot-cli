<?php

declare(strict_types=1);

namespace Dot\Cli\Factory;

use Dot\Cli\FileLockerInterface;
use Laminas\Cli\ContainerCommandLoader;
use Laminas\Cli\Listener\TerminateListener;
use Composer\InstalledVersions;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

/**
 * Class ApplicationFactory
 */
class ApplicationFactory
{
    /**
     * @param ContainerInterface $container
     * @return Application
     */
    public function __invoke(ContainerInterface $container): Application
    {
        $config = $container->get('config')['dot_cli'] ?? [];
        Assert::isMap($config);

        $version = InstalledVersions::getPrettyVersion('laminas/laminas-cli');
        Assert::string($version);

        $commands = $config['commands'] ?? [];
        Assert::isMap($commands);
        Assert::allString($commands);

        $eventDispatcherServiceName = __NAMESPACE__ . '\SymfonyEventDispatcher';
        $dispatcher                 = $container->has($eventDispatcherServiceName)
            ? $container->get($eventDispatcherServiceName)
            : new EventDispatcher();
        Assert::isInstanceOf($dispatcher, EventDispatcherInterface::class);

        $dispatcher->addListener(ConsoleEvents::TERMINATE, new TerminateListener($config));

        $fileLocker = $container->get(FileLockerInterface::class);
        Assert::isInstanceOf($fileLocker, FileLockerInterface::class);

        $application = new \Dot\Cli\Application($fileLocker, $config);
        // phpcs:ignore WebimpressCodingStandard.PHP.CorrectClassNameCase
        $application->setCommandLoader(new ContainerCommandLoader($container, $commands));
        $application->setDispatcher($dispatcher);
        $application->setAutoExit(false);

        return $application;
    }
}
