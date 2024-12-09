<?php

namespace App\Shared\Doctrine\Service;

use App\Modules\Cart\Dto\CartDto;
use App\Modules\Cart\Query\ICartQuery;
use App\Modules\Cart\Service\ICartService;
use App\Modules\Common\Dto\IDto;
use App\Shared\Doctrine\Entity\Cart;
use App\Shared\Doctrine\Entity\IEntity;
use App\Shared\Doctrine\Repository\CartRepository;
use App\Shared\Doctrine\Repository\ProductRepository;
use Symfony\Component\Uid\Uuid;

final class CartEntityService extends EntityService implements ICartService
{
    private ICartQuery $cartQuery;

    public function __construct(
        ICartQuery $cartQuery,
        private CartRepository $cartRepository,
        private ProductRepository $productRepository
    ) {
        $this->cartQuery = $cartQuery;
    }

    public function convertToEntity(IDto|CartDto $dto): IEntity
    {
        $cart = $this->cartRepository->find($dto->getId());
        if(!$cart instanceof Cart) {
            $cart = new Cart(Uuid::fromString($dto->getId()));
        }

        foreach ($dto->getProducts() as $productDto) {
            $product = $this->productRepository->find($productDto->getId());
            if ($product) {
                $cart->addProduct($product);
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