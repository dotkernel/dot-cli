<?php

declare(strict_types=1);

namespace Dot\Cli;

/**
 * Interface FileLocker
 * @package Dot\Cli
 */
interface FileLockerInterface
{
    /**
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * @param bool $enabled
     * @return self
     */
    public function setEnabled(bool $enabled): self;

    /**
     * @return string|null
     */
    public function getDirPath(): ?string;

    /**
     * @param string|null $dirPath
     * @return $this
     */
    public function setDirPath(?string $dirPath): self;

    /**
     * @return string|null
     */
    public function getCommandName(): ?string;

    /**
     * @param string|null $commandName
     * @return $this
     */
    public function setCommandName(?string $commandName): self;

    /**
     * @return string
     */
    public function getLockFilePath(): string;

    /**
     * @return void
     */
    public function lock(): void;

    /**
     * @return void
     */
    public function unlock(): void;
}
