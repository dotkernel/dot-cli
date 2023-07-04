<?php

declare(strict_types=1);

namespace Dot\Cli;

use Exception;

use function fclose;
use function flock;
use function fopen;
use function rtrim;
use function sprintf;
use function str_replace;

use const LOCK_EX;
use const LOCK_NB;
use const LOCK_UN;

class FileLocker implements FileLockerInterface
{
    private bool $enabled;
    private ?string $dirPath;
    private ?string $commandName;
    private mixed $lockFile;

    public function __construct(
        bool $enabled = false,
        ?string $dirPath = null,
        ?string $commandName = null,
        mixed $lockFile = null,
    ) {
        $this->enabled     = $enabled;
        $this->dirPath     = $dirPath;
        $this->commandName = $commandName;
        $this->lockFile    = $lockFile;
    }

    public function initLockFile(): self
    {
        $this->lockFile = fopen($this->getLockFilePath(), 'w+');

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
        $dirPath       = rtrim($dirPath, '/');
        $dirPath       = rtrim($dirPath, '\\');
        $this->dirPath = $dirPath;

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
        return sprintf(
            '%s/command-%s.lock',
            $this->dirPath,
            str_replace(':', '-', $this->commandName)
        );
    }

    /**
     * @inheritDoc
     */
    public function lock(): void
    {
        if (! $this->enabled) {
            return;
        }

        if (empty($this->commandName)) {
            return;
        }

        $this->initLockFile();

        if (! flock($this->lockFile, LOCK_EX | LOCK_NB, $wouldBlock)) {
            if ($wouldBlock) {
                throw new Exception('The file lock is being held by a different process');
            }
        }
    }

    public function unlock(): void
    {
        if (! $this->enabled) {
            return;
        }

        if (empty($this->commandName)) {
            return;
        }

        flock($this->lockFile, LOCK_UN);
        fclose($this->lockFile);
    }
}
