<?php

namespace App\Shared\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CartProduct
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: "App\Shared\Doctrine\Entity\Cart", inversedBy: "cartProducts")]
    private Cart $cart;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: "App\Shared\Doctrine\Entity\Product")]
    private Product $product;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    public function __construct(Cart $cart, Product $product, int $quantity)
    {
        $this->cart = $cart;
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }
}