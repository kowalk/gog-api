<?php

namespace App\Shared\Doctrine\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class Cart implements IEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(type: 'boolean')]
    private bool $isGuest;

    #[ORM\OneToMany(targetEntity: "App\Shared\Doctrine\Entity\CartProduct", mappedBy: "cart", cascade: ["persist", "remove"])]
    private Collection $cartProducts;

    public function __construct(Uuid $id, bool $isGuest = true)
    {
        $this->id = $id;
        $this->isGuest = $isGuest;
        $this->cartProducts = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function isGuest(): bool
    {
        return $this->isGuest;
    }

    public function setIsGuest(bool $isGuest): self
    {
        $this->isGuest = $isGuest;
        return $this;
    }

    public function getCartProducts(): Collection
    {
        return $this->cartProducts;
    }

    public function addProduct(Product $product, int $quantity = 1): self
    {
        foreach ($this->cartProducts as $cartProduct) {
            if ($cartProduct->getProduct() === $product) {
                $cartProduct->setQuantity($quantity);
                return $this;
            }
        }

        $cartProduct = new CartProduct($this, $product, $quantity);
        $this->cartProducts[] = $cartProduct;
        return $this;
    }

    public function removeProduct(Product $product): self
    {
        foreach ($this->cartProducts as $cartProduct) {
            if ($cartProduct->getProduct() === $product) {
                $this->cartProducts->removeElement($cartProduct);
                return $this;
            }
        }
        return $this;
    }

    public function setCartProducts(ArrayCollection $cartProducts): self
    {
        $this->cartProducts = $cartProducts;
        return $this;
    }
}