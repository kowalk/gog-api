<?php

namespace App\Modules\Common\Bus;

interface ICommandBus
{
    public function dispatch(ICommandBus $command, array $stamps = []): void;
}
