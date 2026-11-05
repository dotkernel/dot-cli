<?php

declare(strict_types=1);

namespace Dot\Cli\Exception;

use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;

class InvalidConfigException extends InvalidArgumentException implements
    ContainerExceptionInterface,
    ExceptionInterface
{
    public const string MESSAGE_INVALID_EVENT_DISPATCHER
        = 'The event dispatcher service must implement Symfony\Component\EventDispatcher\EventDispatcherInterface.';

    public static function invalidEventDispatcher(): self
    {
        return new self(self::MESSAGE_INVALID_EVENT_DISPATCHER);
    }
}
