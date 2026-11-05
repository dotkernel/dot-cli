<?php

declare(strict_types=1);

namespace Dot\Cli;

use Dot\Cli\Exception\RuntimeException;

use function fclose;
use function flock;
use function fopen;
use function is_dir;
use function is_resource;
use function is_writable;
use function rtrim;
use function sprintf;
use function str_replace;

use const LOCK_EX;
use const LOCK_NB;
use const LOCK_UN;

class FileLocker implements FileLockerInterface
{
    public function __construct(
        private bool $enabled = false,
        private ?string $dirPath = null,
        private ?string $commandName = null,
        private mixed $lockFile = null,
    ) {
    }

    public function initLockFile(): self
    {
        $path    = $this->getLockFilePath();
        $dirPath = (string) $this->dirPath;
        if (! is_dir($dirPath) || ! is_writable($dirPath)) {
            throw RuntimeException::lockDirectoryNotWritable($dirPath);
        }

        $handle = fopen($path, 'c');
        if ($handle === false) {
            throw RuntimeException::cannotOpenLockFile($path);
        }
        $this->lockFile = $handle;

        return $this;
    }

    public function enable(): self
    {
        $this->enabled = true;

        return $this;
    }

    public function disable(): self
    {
        $this->enabled = false;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getDirPath(): ?string
    {
        return $this->dirPath;
    }

    public function setDirPath(?string $dirPath): self
    {
        $this->dirPath = $dirPath === null ? null : rtrim($dirPath, '/\\');

        return $this;
    }

    public function getCommandName(): ?string
    {
        return $this->commandName;
    }

    public function setCommandName(?string $commandName): self
    {
        $this->commandName = $commandName;

        return $this;
    }

    public function getLockFile(): mixed
    {
        return $this->lockFile;
    }

    public function setLockFile(mixed $lockFile): self
    {
        $this->lockFile = $lockFile;

        return $this;
    }

    public function getLockFilePath(): string
    {
        if ($this->dirPath === null || $this->commandName === null || $this->commandName === '') {
            throw RuntimeException::missingLockFileData();
        }

        return sprintf(
            '%s/command-%s.lock',
            $this->dirPath,
            str_replace(':', '-', $this->commandName)
        );
    }

    public function lock(): void
    {
        if (! $this->enabled || $this->commandName === null || $this->commandName === '') {
            return;
        }

        if (is_resource($this->lockFile)) {
            return;
        }

        $this->initLockFile();
        $handle = $this->lockFile;
        if (! is_resource($handle)) {
            throw RuntimeException::cannotOpenLockFile($this->getLockFilePath());
        }

        if (! flock($handle, LOCK_EX | LOCK_NB, $wouldBlock)) {
            fclose($handle);
            $this->lockFile = null;

            throw $wouldBlock
                ? RuntimeException::lockHeld()
                : RuntimeException::cannotAcquireLock($this->getLockFilePath());
        }
    }

    public function unlock(): void
    {
        if (! is_resource($this->lockFile)) {
            return;
        }

        flock($this->lockFile, LOCK_UN);
        fclose($this->lockFile);
        $this->lockFile = null;
    }
}
