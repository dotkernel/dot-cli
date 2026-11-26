<?php

declare(strict_types=1);

namespace Dot\Cli\Exception;

use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use function sprintf;

class InvalidConfigException extends InvalidArgumentException implements
    ContainerExceptionInterface,
    ExceptionInterface
{
    public const string MESSAGE_INVALID_EVENT_DISPATCHER
        = 'The event dispatcher service must implement "%s".';

    public static function invalidEventDispatcher(): self
    {
        return new self(sprintf(self::MESSAGE_INVALID_EVENT_DISPATCHER, EventDispatcherInterface::class));
    }
}
