<?php

namespace App\Modules\Cart\Command;

use App\Modules\Common\Command\ICommand;

final class RemoveProductFromCartCommand implements ICommand
{
    public function __construct(
        private readonly string $cartId,
        private readonly string $productId
    ) {
    }

    public function getCartId(): string
    {
        return $this->cartId;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }
}