<?php

declare(strict_types=1);

namespace Dot\Cli;

use Dot\Cli\Exception\RuntimeException;
use Exception;

interface FileLockerInterface
{
    /**
     * @throws RuntimeException When the lock directory is not writable or the lock file cannot be opened.
     */
    public function initLockFile(): self;

    public function enable(): self;

    public function disable(): self;

    public function isEnabled(): bool;

    public function setEnabled(bool $enabled): self;

    public function getDirPath(): ?string;

    public function setDirPath(?string $dirPath): self;

    public function getCommandName(): ?string;

    public function setCommandName(?string $commandName): self;

    /**
     * @return resource|null
     */
    public function getLockFile(): mixed;

    public function setLockFile(mixed $lockFile): self;

    /**
     * @throws RuntimeException When the directory or the command name is missing.
     */
    public function getLockFilePath(): string;

    /**
     * Implementations may throw any Exception; Dot\Cli\Application reports it and returns Command::FAILURE.
     *
     * @throws Exception
     */
    public function lock(): void;

    public function unlock(): void;
}
