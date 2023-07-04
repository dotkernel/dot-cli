<?php

declare(strict_types=1);

namespace Dot\Cli;

use Exception;

interface FileLockerInterface
{
    public function initLockFile(): self;

    public function enable(): self;

    public function disable(): self;

    public function isEnabled(): bool;

    public function setEnabled(bool $enabled): self;

    public function getDirPath(): ?string;

    public function setDirPath(?string $dirPath): self;

    public function getCommandName(): ?string;

    public function setCommandName(?string $commandName): self;

    public function getLockFile(): mixed;

    public function setLockFile(mixed $lockFile): self;

    public function getLockFilePath(): string;

    /**
     * @throws Exception
     */
    public function lock(): void;

    public function unlock(): void;
}
