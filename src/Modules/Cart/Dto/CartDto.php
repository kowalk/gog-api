<?php

namespace App\Modules\Cart\Dto;

use App\Modules\Common\Dto\IDto;
use App\Shared\Assert;

final class CartDto implements IDto
{
    public function __construct(
        private string $id,
        private CartProductDtoCollection $products = new CartProductDtoCollection([])
    )  {
        Assert::uuid($id);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProducts(): CartProductDtoCollection
    {
        return $this->products;
    }

    public function addProduct(string $productId, ?int $quantity = null): void
    {
        Assert::uuid($productId);
        $this->products->addCartProduct($productId, $quantity);
    }
}