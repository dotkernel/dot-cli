<?php

declare(strict_types=1);

namespace DotTest\Cli;

use Dot\Cli\FileLocker;
use Exception;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

use function sprintf;

class FileLockerTest extends TestCase
{
    use CommonTrait;

    protected function setup(): void
    {
        $this->fileSystem = vfsStream::setup('root', 0644, [
            'data' => [
                'lock' => [],
            ],
        ]);
        $this->config     = $this->generateConfig(
            sprintf('%s/data/lock', $this->fileSystem->url())
        );
    }

    public function testAccessors(): void
    {
        $fileLocker = new FileLocker();
        $this->assertFalse($fileLocker->isEnabled());
        $fileLocker->enable();
        $this->assertInstanceOf(FileLocker::class, $fileLocker);
        $this->assertTrue($fileLocker->isEnabled());
        $fileLocker->disable();
        $this->assertInstanceOf(FileLocker::class, $fileLocker);
        $this->assertFalse($fileLocker->isEnabled());
        $fileLocker->setEnabled(true);
        $this->assertInstanceOf(FileLocker::class, $fileLocker);
        $this->assertTrue($fileLocker->isEnabled());
        $fileLocker->setEnabled(false);
        $this->assertInstanceOf(FileLocker::class, $fileLocker);
        $this->assertFalse($fileLocker->isEnabled());
        $this->assertNull($fileLocker->getDirPath());
        $fileLocker->setDirPath('test');
        $this->assertInstanceOf(FileLocker::class, $fileLocker);
        $this->assertSame('test', $fileLocker->getDirPath());
        $this->assertNull($fileLocker->getCommandName());
        $fileLocker->setCommandName('test');
        $this->assertInstanceOf(FileLocker::class, $fileLocker);
        $this->assertSame('test', $fileLocker->getCommandName());
        $this->assertNull($fileLocker->getLockFile());
        $fileLocker->setLockFile('test');
        $this->assertInstanceOf(FileLocker::class, $fileLocker);
        $this->assertSame('test', $fileLocker->getLockFile());
        $this->assertSame('test/command-test.lock', $fileLocker->getLockFilePath());
    }

    public function testWillInitLockFile(): void
    {
        $config = $this->getConfig();

        $fileLocker = new FileLocker(true, $config['dirPath'], 'test');
        $this->assertNull($fileLocker->getLockFile());
        $fileLocker->initLockFile();
        $this->assertInstanceOf(FileLocker::class, $fileLocker);
        $this->assertIsResource($fileLocker->getLockFile());
    }

    /**
     * @throws Exception
     */
    public function testWillNotLockWhenDisabled(): void
    {
        $config = $this->getConfig();

        $fileLocker = new FileLocker(false, $config['dirPath'], 'test');
        $this->assertNull($fileLocker->getLockFile());
        $fileLocker->lock();
        $this->assertInstanceOf(FileLocker::class, $fileLocker);
        $this->assertNull($fileLocker->getLockFile());
    }

    /**
     * @throws Exception
     */
    public function testWillNotLockWithoutValidCommandName(): void
    {
        $config = $this->getConfig();

        $fileLocker = new FileLocker(true, $config['dirPath']);
        $this->assertNull($fileLocker->getLockFile());
        $fileLocker->lock();
        $this->assertInstanceOf(FileLocker::class, $fileLocker);
        $this->assertNull($fileLocker->getLockFile());
    }

    /**
     * @throws Exception
     */
    public function testWillLockWhenLockedAndEnabledAndHasValidCommandName(): void
    {
        $config = $this->getConfig();

        $fileLocker = new FileLocker(true, $config['dirPath'], 'test');
        $this->assertNull($fileLocker->getLockFile());
        $fileLocker->lock();
        $this->assertInstanceOf(FileLocker::class, $fileLocker);
        $this->assertIsResource($fileLocker->getLockFile());
        $this->assertFileExists($fileLocker->getLockFilePath());
    }

    public function testWillNotUnlockWhenDisabled(): void
    {
        $config = $this->getConfig();

        $fileLocker = new FileLocker(false, $config['dirPath'], 'test');
        $this->assertNull($fileLocker->getLockFile());
        $fileLocker->unlock();
        $this->assertInstanceOf(FileLocker::class, $fileLocker);
        $this->assertNull($fileLocker->getLockFile());
    }

    public function testWillNotUnlockWithoutValidCommandName(): void
    {
        $config = $this->getConfig();

        $fileLocker = new FileLocker(false, $config['dirPath']);
        $this->assertNull($fileLocker->getLockFile());
        $fileLocker->unlock();
        $this->assertInstanceOf(FileLocker::class, $fileLocker);
        $this->assertNull($fileLocker->getLockFile());
    }

    /**
     * @throws Exception
     */
    public function testWillUnlockWhenLockedAndEnabledAndHasValidCommandName(): void
    {
        $config = $this->getConfig();

        $fileLocker = new FileLocker(true, $config['dirPath'], 'test');
        $fileLocker->lock();
        $this->assertInstanceOf(FileLocker::class, $fileLocker);
        $this->assertIsResource($fileLocker->getLockFile());
        $this->assertFileExists($fileLocker->getLockFilePath());
        $fileLocker->unlock();
        $this->assertInstanceOf(FileLocker::class, $fileLocker);
        $this->assertFileIsReadable($fileLocker->getLockFilePath());
        $this->assertFileIsWritable($fileLocker->getLockFilePath());
    }

    protected function generateConfig(string $dirPath): array
    {
        return [
            'enabled' => true,
            'dirPath' => $dirPath,
        ];
    }
}
