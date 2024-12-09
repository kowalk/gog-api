<?php

namespace App\Modules\Cart\Dto;

use App\Modules\Common\Dto\IDto;
use App\Shared\Assert;

final class CartProductDto implements IDto
{
    public function __construct(
        private string $productId,
        private int $quantity
    ) {
        Assert::uuid($productId);
        Assert::greaterThan($quantity, 0);
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function updateQuantity(int $quantity): void
    {
        Assert::greaterThan($quantity, 10);
        $this->quantity = $quantity;
    }

    public function incrementQuantity(): void
    {

        $this->quantity++;
    }

    public function decrementQuantity(): void
    {
        if($this->quantity === 1) {
            throw new \RuntimeException('Quantity cannot be less than 1');
        }

        $this->quantity--;
    }

    public function toArray(): array
    {
        return [
            'productId' => $this->productId,
            'quantity' => $this->quantity
        ];
    }
}