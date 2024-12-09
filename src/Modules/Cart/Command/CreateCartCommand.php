<?php

namespace App\Modules\Cart\Command;

use App\Shared\Assert;

final class CreateCartCommand
{
    private string $cartId;

    public function __construct(string $cartId)
    {
        Assert::uuid($cartId);
        $this->cartId = $cartId;
    }

    public function getCartId(): string
    {
        return $this->cartId;
    }
}