<?php

declare(strict_types=1);

namespace DotTest\Cli;

use Dot\Cli\FileLockerInterface;

/**
 * Package config for tests that only pass it around. Nothing reads or writes LOCK_DIR_PATH:
 * tests that take a real lock use TempDirectoryTrait.
 */
trait CommonTrait
{
    protected const string LOCK_DIR_PATH = '/dot-cli-test/data/lock';

    /** @var array<string, mixed> */
    protected array $config;

    protected function setUp(): void
    {
        $this->config = $this->generateConfig(self::LOCK_DIR_PATH);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return array<string, mixed>
     */
    protected function generateConfig(string $dirPath): array
    {
        return [
            'dot_cli'                  => [
                'version'  => '1.0.0',
                'name'     => 'Dotkernel CLI',
                'commands' => [
                    'test' => 'test',
                ],
            ],
            FileLockerInterface::class => [
                'enabled' => true,
                'dirPath' => $dirPath,
            ],
        ];
    }
}
