<?php

declare(strict_types=1);

namespace DotTest\Cli;

use Dot\Cli\FileLockerInterface;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

use function sprintf;

trait CommonTrait
{
    protected vfsStreamDirectory $fileSystem;
    protected array $config;

    protected function setup(): void
    {
        $this->fileSystem = vfsStream::setup('root', 0644, [
            'data' => [
                'lock' => [],
            ],
        ]);
        $this->config     = $this->generateConfig(
            sprintf('%s/data/lock', $this->fileSystem->url())
        );
    }

    protected function getConfig(): array
    {
        return $this->config;
    }

    protected function generateConfig(string $dirPath): array
    {
        return [
            'dot_cli'                  => [
                'version'  => '1.0.0',
                'name'     => 'DotKernel CLI',
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
