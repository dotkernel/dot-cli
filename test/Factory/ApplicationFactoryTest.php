<?php

declare(strict_types=1);

namespace DotTest\Cli\Factory;

use Dot\Cli\Application;
use Dot\Cli\Exception\InvalidConfigException;
use Dot\Cli\Factory\ApplicationFactory;
use Dot\Cli\Factory\FileLockerFactory;
use Dot\Cli\FileLocker;
use Dot\Cli\FileLockerInterface;
use DotTest\Cli\CommonTrait;
use DotTest\Cli\Fixture\InMemoryContainer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ApplicationFactoryTest extends TestCase
{
    use CommonTrait;

    public function testWillNotCreateApplicationWithoutConfig(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage(ApplicationFactory::MESSAGE_MISSING_CONFIG);

        (new ApplicationFactory())(new InMemoryContainer());
    }

    /**
     * @return iterable<string, array{mixed, string}>
     */
    public static function invalidConfigProvider(): iterable
    {
        yield 'config is not an array' => ['test', ApplicationFactory::MESSAGE_MISSING_PACKAGE_CONFIG];
        yield 'missing dot_cli' => [['test'], ApplicationFactory::MESSAGE_MISSING_PACKAGE_CONFIG];
        yield 'empty dot_cli' => [['dot_cli' => []], ApplicationFactory::MESSAGE_MISSING_PACKAGE_CONFIG];
        yield 'missing commands' => [['dot_cli' => ['test']], ApplicationFactory::MESSAGE_MISSING_CONFIG_COMMANDS];
        yield 'empty commands' => [
            ['dot_cli' => ['commands' => []]],
            ApplicationFactory::MESSAGE_MISSING_CONFIG_COMMANDS,
        ];
    }

    #[DataProvider('invalidConfigProvider')]
    public function testWillNotCreateApplicationWithInvalidConfig(mixed $config, string $message): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage($message);

        (new ApplicationFactory())(new InMemoryContainer(['config' => $config]));
    }

    public function testWillNotCreateApplicationWithoutFileLockerService(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage(FileLockerFactory::MESSAGE_MISSING_FILE_LOCKER);

        (new ApplicationFactory())(new InMemoryContainer(['config' => $this->getConfig()]));
    }

    public function testWillNotCreateApplicationWithInvalidFileLockerService(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage(FileLockerFactory::MESSAGE_MISSING_FILE_LOCKER);

        (new ApplicationFactory())($this->createContainer()->set(FileLockerInterface::class, new stdClass()));
    }

    public function testWillCreateApplicationWithDefaultDispatcher(): void
    {
        $application = (new ApplicationFactory())($this->createContainer());

        $this->assertSame('Dotkernel CLI', $application->getName());
        $this->assertSame('1.0.0', $application->getVersion());
        $this->assertFalse($application->isAutoExitEnabled());
        $this->assertInstanceOf(EventDispatcher::class, $this->getDispatcher($application));
    }

    public function testUsesTheLaminasDispatcherKey(): void
    {
        $dispatcher = new EventDispatcher();
        $container  = $this->createContainer()->set('Laminas\Cli\SymfonyEventDispatcher', $dispatcher);

        $application = (new ApplicationFactory())($container);

        $this->assertSame($dispatcher, $this->getDispatcher($application));
    }

    public function testUsesTheLegacyDispatcherKey(): void
    {
        $dispatcher = new EventDispatcher();
        $container  = $this->createContainer()->set('Dot\Cli\Factory\SymfonyEventDispatcher', $dispatcher);

        $application = (new ApplicationFactory())($container);

        $this->assertSame($dispatcher, $this->getDispatcher($application));
    }

    public function testLaminasDispatcherKeyWinsOverLegacyKey(): void
    {
        $dispatcher = new EventDispatcher();
        $container  = $this->createContainer()
            ->set(ApplicationFactory::EVENT_DISPATCHER, $dispatcher)
            ->set(ApplicationFactory::LEGACY_EVENT_DISPATCHER, new EventDispatcher());

        $application = (new ApplicationFactory())($container);

        $this->assertSame($dispatcher, $this->getDispatcher($application));
    }

    public function testRejectsNonDispatcherService(): void
    {
        $container = $this->createContainer()->set(ApplicationFactory::EVENT_DISPATCHER, new stdClass());

        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage(InvalidConfigException::MESSAGE_INVALID_EVENT_DISPATCHER);

        (new ApplicationFactory())($container);
    }

    private function createContainer(): InMemoryContainer
    {
        return new InMemoryContainer([
            'config'                   => $this->getConfig(),
            FileLockerInterface::class => new FileLocker(),
        ]);
    }

    private function getDispatcher(Application $application): EventDispatcherInterface
    {
        $dispatcher = (new ReflectionProperty(\Symfony\Component\Console\Application::class, 'dispatcher'))
            ->getValue($application);
        $this->assertInstanceOf(EventDispatcherInterface::class, $dispatcher);

        return $dispatcher;
    }
}
