<?php

declare(strict_types=1);

namespace Dot\Cli;

use Exception;

use function fclose;
use function file_exists;
use function flock;
use function fopen;
use function is_dir;
use function mkdir;
use function rtrim;
use function sprintf;
use function unlink;

/**
 * Class FileLocker
 * @package Dot\Cli
 */
class FileLocker implements FileLockerInterface
{
    private bool $enabled;
    private ?string $dirPath;
    private ?string $commandName;

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDirPath(): ?string
    {
        return $this->dirPath;
    }

    /**
     * @param string|null $dirPath
     * @return $this
     */
    public function setDirPath(?string $dirPath): self
    {
        $dirPath = rtrim($dirPath, '/');
        $dirPath = rtrim($dirPath, '\\');
        $this->dirPath = $dirPath;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCommandName(): ?string
    {
        return $this->commandName;
    }

    /**
     * @param string|null $commandName
     * @return $this
     */
    public function setCommandName(?string $commandName): self
    {
        $this->commandName = $commandName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLockFilePath(): string
    {
        $commandName = str_replace(':', '-', $this->commandName);
        return sprintf('%s/command-%s.lock', $this->dirPath, $commandName);
    }

    /**
     * @throws Exception
     * @return void
     */
    public function lock(): void
    {
        if (!$this->enabled) {
            return;
        }

        if (!is_dir($this->dirPath)) {
            mkdir($this->dirPath);
        }

        $lockFile = $this->getLockFilePath();
        $fp = fopen($lockFile, 'w+');
        if (!flock($fp, LOCK_EX|LOCK_NB, $wouldBlock)) {
            if ($wouldBlock) {
                throw new Exception('Another process holds the lock!');
            }
        }
        fclose($fp);
    }

    /**
     * @return void
     */
    public function unlock(): void
    {
        if (!$this->enabled) {
            return;
        }

        $lockFile = $this->getLockFilePath();
        if (!file_exists($lockFile)) {
            return;
        }

        $fp = fopen($lockFile, 'w+');
        flock($fp, LOCK_UN);
        fclose($fp);
        unlink($lockFile);
    }
}
