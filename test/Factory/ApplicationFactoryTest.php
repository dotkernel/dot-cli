<?php

declare(strict_types=1);

namespace DotTest\Cli\Factory;

use Dot\Cli\Application;
use Dot\Cli\Factory\ApplicationFactory;
use Dot\Cli\Factory\FileLockerFactory;
use Dot\Cli\FileLockerInterface;
use DotTest\Cli\CommonTrait;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ApplicationFactoryTest extends TestCase
{
    use CommonTrait;

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateApplicationWithoutConfig(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())
            ->method('has')
            ->with('config')
            ->willReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(ApplicationFactory::MESSAGE_MISSING_CONFIG);
        (new ApplicationFactory())($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateApplicationWithoutPackageConfig(): void
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
        $this->expectExceptionMessage(ApplicationFactory::MESSAGE_MISSING_PACKAGE_CONFIG);
        (new ApplicationFactory())($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateApplicationWithoutConfigCommands(): void
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
                'dot_cli' => [
                    'test',
                ],
            ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(ApplicationFactory::MESSAGE_MISSING_CONFIG_COMMANDS);
        (new ApplicationFactory())($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateApplicationWithoutFileLockerService(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->method('has')->willReturnMap([
            ['config', true],
            ['Dot\Cli\Factory\SymfonyEventDispatcher', false],
            [FileLockerInterface::class, false],
        ]);

        $container->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($this->getConfig());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(FileLockerFactory::MESSAGE_MISSING_FILE_LOCKER);
        (new ApplicationFactory())($container);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testWillCreateApplication(): void
    {
        $container  = $this->createMock(ContainerInterface::class);
        $fileLocker = $this->createMock(FileLockerInterface::class);

        $container->method('has')->willReturnMap([
            ['config', true],
            ['Dot\Cli\Factory\SymfonyEventDispatcher', false],
            [FileLockerInterface::class, true],
        ]);

        $container->method('get')->willReturnMap([
            ['config', $this->getConfig()],
            [FileLockerInterface::class, $fileLocker],
        ]);

        $application = (new ApplicationFactory())($container);
        $this->assertInstanceOf(Application::class, $application);
    }
}
