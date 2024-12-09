<?php

namespace App\Modules\Cart\Command;

final class AddProductToCartCommand
{
    private string $cartId;
    private string $productId;

    public function __construct(string $cartId, string $productId)
    {
        $this->cartId = $cartId;
        $this->productId = $productId;
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