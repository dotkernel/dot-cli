<?php

declare(strict_types=1);

namespace DotTest\Cli\Factory;

use Dot\Cli\Exception\InvalidConfigException;
use Dot\Cli\Factory\ApplicationFactory;
use Dot\Cli\Factory\FileLockerFactory;
use Dot\Cli\FileLocker;
use Dot\Cli\FileLockerInterface;
use DotTest\Cli\CommonTrait;
use DotTest\Cli\Fixture\InMemoryContainer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;

class FileLockerFactoryTest extends TestCase
{
    use CommonTrait;

    public function testWillNotCreateServiceWithoutConfig(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage(ApplicationFactory::MESSAGE_MISSING_CONFIG);

        (new FileLockerFactory())(new InMemoryContainer());
    }

    /**
     * @return iterable<string, array{mixed, string}>
     */
    public static function invalidConfigProvider(): iterable
    {
        yield 'config is not an array' => ['test', FileLockerFactory::MESSAGE_MISSING_FILE_LOCKER_CONFIG];
        yield 'missing locker config' => [['test'], FileLockerFactory::MESSAGE_MISSING_FILE_LOCKER_CONFIG];
        yield 'empty locker config' => [
            [FileLockerInterface::class => []],
            FileLockerFactory::MESSAGE_MISSING_FILE_LOCKER_CONFIG,
        ];
        yield 'missing enabled' => [
            [FileLockerInterface::class => ['dirPath' => 'test']],
            FileLockerFactory::MESSAGE_MISSING_FILE_LOCKER_CONFIG_ENABLED,
        ];
        yield 'enabled is not a bool' => [
            [FileLockerInterface::class => ['enabled' => 1, 'dirPath' => 'test']],
            FileLockerFactory::MESSAGE_MISSING_FILE_LOCKER_CONFIG_ENABLED,
        ];
        yield 'missing dirPath' => [
            [FileLockerInterface::class => ['enabled' => false]],
            FileLockerFactory::MESSAGE_MISSING_FILE_LOCKER_CONFIG_DIR_PATH,
        ];
        yield 'empty dirPath' => [
            [FileLockerInterface::class => ['enabled' => false, 'dirPath' => '']],
            FileLockerFactory::MESSAGE_MISSING_FILE_LOCKER_CONFIG_DIR_PATH,
        ];
    }

    #[DataProvider('invalidConfigProvider')]
    public function testWillNotCreateServiceWithInvalidConfig(mixed $config, string $message): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage($message);

        (new FileLockerFactory())(new InMemoryContainer(['config' => $config]));
    }

    public function testCanCreateService(): void
    {
        $service = (new FileLockerFactory())(new InMemoryContainer(['config' => $this->getConfig()]));

        $this->assertInstanceOf(FileLocker::class, $service);
        $this->assertTrue($service->isEnabled());
        $this->assertSame(self::LOCK_DIR_PATH, $service->getDirPath());
    }

    public function testTrimsTrailingSeparatorFromDirPath(): void
    {
        $container = new InMemoryContainer([
            'config' => [FileLockerInterface::class => ['enabled' => true, 'dirPath' => '/var/lock/']],
        ]);

        $this->assertSame('/var/lock', (new FileLockerFactory())($container)->getDirPath());
    }

    public function testConfigExceptionIsAContainerException(): void
    {
        $this->expectException(ContainerExceptionInterface::class);

        (new FileLockerFactory())(new InMemoryContainer());
    }
}
