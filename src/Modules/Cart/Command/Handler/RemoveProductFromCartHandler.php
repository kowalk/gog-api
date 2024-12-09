<?php

namespace App\Modules\Cart\Command\Handler;

use App\Modules\Cart\Command\RemoveProductFromCartCommand;
use App\Modules\Cart\Command\Validator\RemoveProductFromCartValidator;
use App\Modules\Cart\Query\ICartQuery;
use App\Modules\Cart\Service\ICartService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RemoveProductFromCartHandler
{
    public function __construct(
        private readonly ICartQuery $cartQuery,
        private readonly ICartService $cartService,
        private readonly RemoveProductFromCartValidator $validator
    ) {
    }

    public function __invoke(RemoveProductFromCartCommand $command): void
    {
        $this->validator->validate($command);
        $cart->removeProductById($command->getProductId());
        $this->cartQuery->save($cart);
    }
}