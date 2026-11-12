<?php

declare(strict_types=1);

namespace DotTest\Cli;

use Dot\Cli\Exception\RuntimeException;
use Dot\Cli\FileLocker;
use PHPUnit\Framework\TestCase;

use function chmod;
use function function_exists;
use function mkdir;
use function posix_geteuid;
use function rmdir;

class FileLockerTest extends TestCase
{
    use TempDirectoryTrait;

    public function testAccessors(): void
    {
        $fileLocker = new FileLocker();
        $this->assertFalse($fileLocker->isEnabled());
        $this->assertSame($fileLocker, $fileLocker->enable());
        $this->assertTrue($fileLocker->isEnabled());
        $this->assertSame($fileLocker, $fileLocker->disable());
        $this->assertFalse($fileLocker->isEnabled());
        $this->assertSame($fileLocker, $fileLocker->setEnabled(true));
        $this->assertTrue($fileLocker->isEnabled());
        $this->assertSame($fileLocker, $fileLocker->setEnabled(false));
        $this->assertFalse($fileLocker->isEnabled());
        $this->assertNull($fileLocker->getDirPath());
        $this->assertSame($fileLocker, $fileLocker->setDirPath('test'));
        $this->assertSame('test', $fileLocker->getDirPath());
        $this->assertNull($fileLocker->getCommandName());
        $this->assertSame($fileLocker, $fileLocker->setCommandName('test'));
        $this->assertSame('test', $fileLocker->getCommandName());
        $this->assertNull($fileLocker->getLockFile());
        $this->assertSame($fileLocker, $fileLocker->setLockFile('test'));
        $this->assertSame('test', $fileLocker->getLockFile());
        $this->assertSame('test/command-test.lock', $fileLocker->getLockFilePath());
    }

    public function testSetDirPathAcceptsNull(): void
    {
        $fileLocker = new FileLocker(false, 'test');

        $fileLocker->setDirPath(null);

        $this->assertNull($fileLocker->getDirPath());
    }

    public function testSetDirPathTrimsTrailingSeparators(): void
    {
        $fileLocker = (new FileLocker())->setDirPath('/var/lock/\\/');

        $this->assertSame('/var/lock', $fileLocker->getDirPath());
    }

    public function testGetLockFilePathReplacesColons(): void
    {
        $fileLocker = new FileLocker(true, '/var/lock', 'demo:command');

        $this->assertSame('/var/lock/command-demo-command.lock', $fileLocker->getLockFilePath());
    }

    public function testGetLockFilePathRequiresCommandName(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(RuntimeException::MESSAGE_MISSING_LOCK_DATA);

        (new FileLocker(true, $this->tempDir))->getLockFilePath();
    }

    public function testGetLockFilePathRequiresDirPath(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(RuntimeException::MESSAGE_MISSING_LOCK_DATA);

        (new FileLocker(true, null, 'test'))->getLockFilePath();
    }

    public function testWillInitLockFile(): void
    {
        $fileLocker = new FileLocker(true, $this->tempDir, 'test');
        $this->assertNull($fileLocker->getLockFile());

        $this->assertSame($fileLocker, $fileLocker->initLockFile());
        $this->assertIsResource($fileLocker->getLockFile());
        $fileLocker->unlock();
    }

    public function testInitLockFileThrowsWhenDirectoryIsMissing(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('does not exist or is not writable');

        (new FileLocker(true, $this->tempDir . '/missing', 'test'))->initLockFile();
    }

    public function testLockThrowsWhenDirectoryIsMissing(): void
    {
        $fileLocker = new FileLocker(true, $this->tempDir . '/missing', 'test');

        try {
            $fileLocker->lock();
            $this->fail('Expected RuntimeException');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('does not exist or is not writable', $exception->getMessage());
        }

        $this->assertNull($fileLocker->getLockFile());
    }

    public function testLockThrowsWhenDirectoryIsNotWritable(): void
    {
        if (function_exists('posix_geteuid') && posix_geteuid() === 0) {
            $this->markTestSkipped('root can write to any directory');
        }

        $dir = $this->tempDir . '/read-only';
        mkdir($dir, 0555);

        try {
            $this->expectException(RuntimeException::class);
            (new FileLocker(true, $dir, 'test'))->lock();
        } finally {
            chmod($dir, 0775);
            rmdir($dir);
        }
    }

    public function testWillNotLockWhenDisabled(): void
    {
        $fileLocker = new FileLocker(false, $this->tempDir, 'test');

        $fileLocker->lock();

        $this->assertNull($fileLocker->getLockFile());
        $this->assertFileDoesNotExist($this->tempDir . '/command-test.lock');
    }

    public function testWillNotLockWithoutCommandName(): void
    {
        $fileLocker = new FileLocker(true, $this->tempDir);

        $fileLocker->lock();

        $this->assertNull($fileLocker->getLockFile());
    }

    public function testWillNotLockWithEmptyCommandName(): void
    {
        $fileLocker = new FileLocker(true, $this->tempDir, '');

        $fileLocker->lock();

        $this->assertNull($fileLocker->getLockFile());
    }

    public function testWillLockWhenEnabledAndHasCommandName(): void
    {
        $fileLocker = new FileLocker(true, $this->tempDir, 'test');

        $fileLocker->lock();

        $this->assertIsResource($fileLocker->getLockFile());
        $this->assertFileExists($fileLocker->getLockFilePath());
        $fileLocker->unlock();
    }

    public function testSecondLockerIsRejected(): void
    {
        $first = new FileLocker(true, $this->tempDir, 'test');
        $first->lock();

        $second = new FileLocker(true, $this->tempDir, 'test');
        try {
            $second->lock();
            $this->fail('Expected RuntimeException');
        } catch (RuntimeException $exception) {
            $this->assertSame(RuntimeException::MESSAGE_LOCK_HELD, $exception->getMessage());
        }

        $this->assertNull($second->getLockFile());
        $first->unlock();
    }

    public function testLockIsAvailableAgainAfterUnlock(): void
    {
        $first = new FileLocker(true, $this->tempDir, 'test');
        $first->lock();
        $first->unlock();

        $second = new FileLocker(true, $this->tempDir, 'test');
        $second->lock();

        $this->assertIsResource($second->getLockFile());
        $second->unlock();
    }

    public function testLockTwiceKeepsOneHandle(): void
    {
        $fileLocker = new FileLocker(true, $this->tempDir, 'test');
        $fileLocker->lock();
        $handle = $fileLocker->getLockFile();

        $fileLocker->lock();

        $this->assertSame($handle, $fileLocker->getLockFile());
        $fileLocker->unlock();
    }

    public function testUnlockBeforeLockIsNoop(): void
    {
        $fileLocker = new FileLocker(true, $this->tempDir, 'test');

        $fileLocker->unlock();

        $this->assertNull($fileLocker->getLockFile());
    }

    public function testUnlockTwiceIsNoop(): void
    {
        $fileLocker = new FileLocker(true, $this->tempDir, 'test');
        $fileLocker->lock();

        $fileLocker->unlock();
        $fileLocker->unlock();

        $this->assertNull($fileLocker->getLockFile());
    }

    public function testUnlockAfterDisableReleasesTheLock(): void
    {
        $fileLocker = new FileLocker(true, $this->tempDir, 'test');
        $fileLocker->lock();
        $fileLocker->disable();

        $fileLocker->unlock();

        $this->assertNull($fileLocker->getLockFile());
        $other = new FileLocker(true, $this->tempDir, 'test');
        $other->lock();
        $this->assertIsResource($other->getLockFile());
        $other->unlock();
    }

    public function testUnlockKeepsTheLockFile(): void
    {
        $fileLocker = new FileLocker(true, $this->tempDir, 'test');
        $fileLocker->lock();

        $fileLocker->unlock();

        $this->assertFileIsReadable($fileLocker->getLockFilePath());
        $this->assertFileIsWritable($fileLocker->getLockFilePath());
    }
}
