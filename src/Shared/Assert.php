<?php

namespace App\Shared;

use App\Modules\Common\IAssert;
use App\Shared\Exception\InvalidArgumentException;

final class Assert extends \Webmozart\Assert\Assert implements IAssert
{
    /**
     * @param string $message
     */
    protected static function reportInvalidArgument($message): void
    {
        throw new InvalidArgumentException($message);
    }
}
