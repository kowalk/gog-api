<?php

namespace App\Shared\Query;

use App\Modules\Cart\Dto\CartDto;
use App\Modules\Cart\Query\ICartQuery;
use App\Modules\Common\Dto\IDtoCollection;
use App\Modules\Common\Dto\IProductDto;
use App\Shared\Doctrine\Collection\DtoCollection;
use App\Shared\Doctrine\Entity\Cart;
use App\Shared\Doctrine\Entity\IEntity;
use App\Shared\Doctrine\Repository\CartRepository;
use App\Shared\Doctrine\Repository\ProductRepository;

final readonly class CartQuery implements ICartQuery, IDoctrineQuery
{
    public function __construct(
        private CartRepository    $cartRepository,
        private ProductRepository $productRepository,
        private ProductQuery      $productQuery
    ) {
    }

    public function findCartById(string $cartId): ?CartDto
    {
        $cart = $this->cartRepository->find($cartId);

        if($cart instanceof Cart) {
            return $this->convertEntityToDto($cart);
        }

        return null;
    }

    public function findCartByName(string $name): ?CartDto
    {
        $cart = $this->cartRepository->findOneBy(['name' => $name]);

        if($cart instanceof Cart) {
            return $this->convertEntityToDto($cart);
        }

        return null;
    }

    public function findAllCarts($limit, $offset): IDtoCollection
    {
        $carts = $this->cartRepository->findBy([], null, $limit, $offset);
        $dtos = [];

        foreach ($carts as $cart) {
            $dtos[] = $this->convertEntityToDto($cart);
        }

        return new DtoCollection($dtos, [CartDto::class]);
    }

    public function getCartById(string $cartId): CartDto
    {
        $cart = $this->findCartById($cartId);

        if(!$cart instanceof CartDto) {
            throw new \RuntimeException('Cart not found');
        }

        return $cart;
    }

    public function deleteCartById(string $cartId): void
    {
        $cart = $this->cartRepository->findCartById($cartId);

        if($cart instanceof Cart) {
            $this->cartRepository->delete($cart);
        }
    }

    public function convertEntityToDto(IEntity|Cart $entity): CartDto
    {
        if(!$entity instanceof Cart) {
            throw new \RuntimeException('Invalid entity');
        }

        $productIds = array_map(fn($product) => $product->getId(), $entity->getProducts()->toArray());
        $products = $this->productRepository->findAllByIds($productIds);
        $productDtos = array_map(fn($product) => $this->productQuery->convertEntityToDto($product), $products);

        return new CartDto(
            $entity->getId()->toString(),
            new DtoCollection($productDtos, [IProductDto::class]
        ));
    }
}