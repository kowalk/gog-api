<?php

namespace App\Modules\Cart\Command;

final readonly class AddProductToCartCommand
{
    public function __construct(
        private string $cartId,
        private string $productId,
        private ?int   $quantity = null
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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }
}