<?php

namespace App\Shared\Doctrine\Service;

use App\Modules\Common\Dto\IDto;
use App\Modules\Product\Dto\ProductDto;
use App\Modules\Product\Service\IProductService;
use App\Shared\Assert;
use App\Shared\Doctrine\Entity\IEntity;
use App\Shared\Doctrine\Entity\Product;
use App\Shared\Doctrine\Repository\CurrencyRepository;
use App\Shared\Doctrine\Repository\ProductRepository;
use Symfony\Component\Uid\Uuid;

final class ProductEntityService extends EntityService implements IProductService
{
    public function __construct(
        private readonly ProductRepository  $productEntityRepository,
        private readonly CurrencyRepository $currencyEntityRepository)
    {
    }

    public function convertToEntity(ProductDto|IDto $dto): IEntity
    {
        Assert::isInstanceOf($dto, ProductDto::class);

        $product = null;
        if($dto->getId() !== null) {
            $product = $this->productEntityRepository->findOneBy(['id' => $dto->getId()]);
        }

        //@var Currency $currency
        $currency = $this->currencyEntityRepository->findOneBy(['code' => $dto->getCurrencyCode()]);

        if($product instanceof Product) {
            $product->setName($dto->getName());
            $product->setPrice($dto->getPrice());
            $product->setCurrency($currency);
        } else {
            $product = new Product(
                Uuid::fromString($dto->getId()),
                $dto->getName(),
                $dto->getPrice(),
                $currency
            );
        }

        return $product;
    }

    public function save(ProductDto|IDto $dto): void
    {
        Assert::isInstanceOf($dto, ProductDto::class);

        $productEntity = $this->convertToEntity($dto);
        $this->productEntityRepository->save($productEntity);
    }

    public function delete(ProductDto|IDto $dto): void
    {
        Assert::isInstanceOf($dto, ProductDto::class);

        $productEntity = $this->convertToEntity($dto);
        $this->productEntityRepository->delete($productEntity);
    }
}
