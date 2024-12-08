<?php

namespace App\Shared;

use Shared\Exception\InvalidArgumentException;

final class Assert extends \Webmozart\Assert\Assert
{
    /**
     * @param string $message
     */
    protected static function reportInvalidArgument($message): void
    {
        throw new InvalidArgumentException($message);
    }
}
