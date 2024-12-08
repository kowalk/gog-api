<?php

namespace App\Shared\Bus;

use App\Modules\Common\Bus\ICommandBus;
use App\Shared\Assert;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\StampInterface;


final readonly class MessengerCommandBus implements ICommandBus
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function dispatch(mixed $command, array $stamps = []): void
    {
        Assert::allIsInstanceOf($stamps, StampInterface::class);
        $this->commandBus->dispatch($command, $stamps);
    }
}
