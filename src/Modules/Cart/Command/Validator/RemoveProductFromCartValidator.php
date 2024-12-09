<?php

namespace App\Modules\Cart\Command\Validator;

use App\Modules\Cart\Command\RemoveProductFromCartCommand;
use App\Modules\Cart\Query\ICartQuery;

final class RemoveProductFromCartValidator
{
    public function __construct(private ICartQuery $cartQuery)
    {
    }

    public function validate(RemoveProductFromCartCommand $command): void
    {
        $cart = $this->cartQuery->findCartById($command->getCartId());

        if (!$cart) {
            throw new \InvalidArgumentException('Cart not found');
        }

        if (empty($command->getCartId())) {
            throw new \InvalidArgumentException('Cart ID is required');
        }

        if (empty($command->getProductId())) {
            throw new \InvalidArgumentException('Product ID is required');
        }
    }
}