<?php

declare(strict_types=1);

namespace DotTest\Cli\Factory;

use Dot\Cli\Factory\ApplicationFactory;
use Dot\Cli\Factory\FileLockerFactory;
use Dot\Cli\FileLocker;
use Dot\Cli\FileLockerInterface;
use DotTest\Cli\CommonTrait;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class FileLockerFactoryTest extends TestCase
{
    use CommonTrait;

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateServiceWithoutConfig(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())
            ->method('has')
            ->with('config')
            ->willReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(ApplicationFactory::MESSAGE_MISSING_CONFIG);
        (new FileLockerFactory())($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateServiceWithoutFileLockerConfig(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())
            ->method('has')
            ->with('config')
            ->willReturn(true);

        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn([
                'test',
            ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(FileLockerFactory::MESSAGE_MISSING_FILE_LOCKER_CONFIG);
        (new FileLockerFactory())($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateServiceWithoutFileLockerConfigEnabled(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())
            ->method('has')
            ->with('config')
            ->willReturn(true);

        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn([
                FileLockerInterface::class => [
                    'dirPath' => 'test',
                ],
            ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(FileLockerFactory::MESSAGE_MISSING_FILE_LOCKER_CONFIG_ENABLED);
        (new FileLockerFactory())($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateServiceWithoutFileLockerConfigDirPath(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())
            ->method('has')
            ->with('config')
            ->willReturn(true);

        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn([
                FileLockerInterface::class => [
                    'enabled' => false,
                ],
            ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(FileLockerFactory::MESSAGE_MISSING_FILE_LOCKER_CONFIG_DIR_PATH);
        (new FileLockerFactory())($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testCanCreateService(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())
            ->method('has')
            ->with('config')
            ->willReturn(true);

        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($this->getConfig());

        $service = (new FileLockerFactory())($container);
        $this->assertInstanceOf(FileLocker::class, $service);
    }
}
