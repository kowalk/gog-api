<?php

namespace App\Shared\Doctrine\Service;

use App\Modules\Cart\Dto\CartDto;
use App\Modules\Cart\Dto\CartProductDto;
use App\Modules\Cart\Query\ICartQuery;
use App\Modules\Cart\Service\ICartService;
use App\Modules\Common\Dto\IDto;
use App\Shared\Doctrine\Entity\Cart;
use App\Shared\Doctrine\Entity\CartProduct;
use App\Shared\Doctrine\Entity\IEntity;
use App\Shared\Doctrine\Repository\CartRepository;
use App\Shared\Doctrine\Repository\ProductRepository;
use Symfony\Component\Uid\Uuid;

final class CartEntityService extends EntityService implements ICartService
{

    public function __construct(
        private readonly ICartQuery            $cartQuery,
        private readonly CartRepository        $cartRepository,
        private readonly ProductRepository     $productRepository,
    ) {
    }

    public function convertToEntity(IDto|CartDto $dto): IEntity
    {
        $cart = $this->cartRepository->find($dto->getId());
        if (!$cart instanceof Cart) {
            $cart = new Cart(Uuid::fromString($dto->getId()));
        }

        $productsCollection = $dto->getProducts();
        $cartProducts = $cart->getCartProducts();

        //replace quantity in cart products if cart product not exists add new cart product to cartProducts
        /** @var CartProductDto $product */
        foreach ($productsCollection->toArray() as $product) {
            $cartProduct = $cartProducts->filter(function (CartProduct $cartProduct) use ($product) {
                return $cartProduct->getProduct()->getId()->toString() === $product->getProductId();
            })->first();

            if ($cartProduct) {
                $cartProduct->setQuantity($product->getQuantity());
            } else {
                $productEntity = $this->productRepository->find($product->getProductId());
                $cart->addProduct($productEntity, $product->getQuantity());
            }
        }

        return $cart;
    }

    public function save(IDto $dto): void
    {
        $entity = $this->convertToEntity($dto);
        $this->cartRepository->save($entity);
    }

    public function delete(IDto|CartDto $dto): void
    {
        $this->cartQuery->deleteCartById($dto->getId());
    }
}