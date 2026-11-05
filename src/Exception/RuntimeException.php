<?php

declare(strict_types=1);

namespace Dot\Cli\Exception;

use function sprintf;

class RuntimeException extends \RuntimeException implements ExceptionInterface
{
    public const string MESSAGE_LOCK_HELD           = 'The file lock is being held by a different process';
    public const string MESSAGE_CANNOT_ACQUIRE_LOCK = 'Unable to acquire the file lock "%s".';
    public const string MESSAGE_LOCK_DIR            = 'The lock directory "%s" does not exist or is not writable.';
    public const string MESSAGE_CANNOT_OPEN_LOCK    = 'Unable to open the lock file "%s".';
    public const string MESSAGE_MISSING_LOCK_DATA   = 'The lock file path needs a lock directory and a command name.';

    public static function lockHeld(): self
    {
        return new self(self::MESSAGE_LOCK_HELD);
    }

    public static function cannotAcquireLock(string $path): self
    {
        return new self(sprintf(self::MESSAGE_CANNOT_ACQUIRE_LOCK, $path));
    }

    public static function lockDirectoryNotWritable(string $dirPath): self
    {
        return new self(sprintf(self::MESSAGE_LOCK_DIR, $dirPath));
    }

    public static function cannotOpenLockFile(string $path): self
    {
        return new self(sprintf(self::MESSAGE_CANNOT_OPEN_LOCK, $path));
    }

    public static function missingLockFileData(): self
    {
        return new self(self::MESSAGE_MISSING_LOCK_DATA);
    }
}
