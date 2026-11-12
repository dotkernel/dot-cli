<?php

declare(strict_types=1);

namespace DotTest\Cli\Fixture;

use Psr\Container\ContainerInterface;

use function array_key_exists;

/**
 * Minimal PSR-11 container for tests.
 */
final class InMemoryContainer implements ContainerInterface
{
    /**
     * @param array<string, mixed> $services
     */
    public function __construct(private array $services = [])
    {
    }

    public function set(string $id, mixed $service): self
    {
        $this->services[$id] = $service;

        return $this;
    }

    public function get(string $id): mixed
    {
        if (! array_key_exists($id, $this->services)) {
            throw NotFoundException::forService($id);
        }

        return $this->services[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }
}
