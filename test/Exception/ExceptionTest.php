<?php

declare(strict_types=1);

namespace DotTest\Cli\Exception;

use Dot\Cli\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    public function testRuntimeExceptionNamedConstructors(): void
    {
        $this->assertSame(RuntimeException::MESSAGE_LOCK_HELD, RuntimeException::lockHeld()->getMessage());
        $this->assertSame(
            'Unable to acquire the file lock "/a.lock".',
            RuntimeException::cannotAcquireLock('/a.lock')->getMessage()
        );
        $this->assertSame(
            'The lock directory "/a" does not exist or is not writable.',
            RuntimeException::lockDirectoryNotWritable('/a')->getMessage()
        );
        $this->assertSame(
            'Unable to open the lock file "/a.lock".',
            RuntimeException::cannotOpenLockFile('/a.lock')->getMessage()
        );
        $this->assertSame(
            RuntimeException::MESSAGE_MISSING_LOCK_DATA,
            RuntimeException::missingLockFileData()->getMessage()
        );
    }
}
