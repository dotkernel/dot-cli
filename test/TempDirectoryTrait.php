<?php

declare(strict_types=1);

namespace DotTest\Cli;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;

use function glob;
use function is_dir;
use function mkdir;
use function rmdir;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

/**
 * A real directory for lock tests. An in-memory stream wrapper such as vfsStream cannot report lock
 * contention: flock() fails but sets $wouldBlock to 0.
 */
trait TempDirectoryTrait
{
    protected string $tempDir;

    #[Before]
    protected function createTempDirectory(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/' . uniqid('dot-cli-test-', true);
        mkdir($this->tempDir, 0775, true);
    }

    #[After]
    protected function removeTempDirectory(): void
    {
        if (! is_dir($this->tempDir)) {
            return;
        }

        foreach (glob($this->tempDir . '/*') ?: [] as $file) {
            unlink($file);
        }
        rmdir($this->tempDir);
    }
}
