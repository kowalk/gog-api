<?php

namespace App\Modules\Cart\Dto;

use App\Modules\Common\Dto\IDto;
use App\Modules\Common\Dto\IDtoCollection;
use App\Modules\Common\Dto\IProductDto;
use App\Shared\Assert;

final class CartDto implements IDto
{
    public function __construct(private string $id, private IDtoCollection $products)
    {
        Assert::uuid($id);
        Assert::allIsInstanceOf($products->toArray(), IProductDto::class);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProducts(): IDtoCollection
    {
        return $this->products;
    }

    public function addProduct(IProductDto $product): void
    {
        $this->products->add($product);
    }
}