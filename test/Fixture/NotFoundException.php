<?php

declare(strict_types=1);

namespace DotTest\Cli\Fixture;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

use function sprintf;

final class NotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
    public static function forService(string $id): self
    {
        return new self(sprintf('Service "%s" not found.', $id));
    }
}
