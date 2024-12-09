<?php
namespace App\Modules\Cart\Dto;

use App\Shared\Assert;

final class CartProductDtoCollection
{
    /**
     * @var array <string, CartProductDto>
     */
    private array $products;

    /**
     * @param array $products<CartProductDto>
     */
    public function __construct(array $products)
    {
        Assert::allIsInstanceOf($products, CartProductDto::class);

        $this->products = [];
        foreach($products as $product) {
            if(isset($this->products[$product->getProductId()])) {
                throw new \RuntimeException('Products should be unique');
            }

            $this->products[$product->getProductId()] = $product;
        }
    }

    public function addCartProduct(string $productId, ?int $quantity = null): void
    {
        if(isset($this->products[$productId]) && $quantity) {
            $this->products[$productId]->updateQuantity($quantity);
            return;
        }

        if(isset($this->products[$productId])) {
            $this->products[$productId]->incrementQuantity();
            return;
        }

        $this->products[$productId] = new CartProductDto($productId, $quantity ?? 1);
    }

    public function removeCartProduct(string $productId): void
    {
        if(!isset($this->products[$productId])) {
            throw new \RuntimeException('Product not found');
        }

        if($this->products[$productId]->getQuantity() > 1) {
            $this->products[$productId]->decrementQuantity();
            return;
        }

        unset($this->products[$productId]);
    }

    public function getProduct(string $productId): ?CartProductDto
    {
        return $this->products[$productId] ?? null;
    }

    /**
     * @return array<CartProductDto>
     */
    public function toArray(): array
    {
        return $this->products;
    }
}