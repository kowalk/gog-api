<?php

namespace App\Shared\Doctrine\Repository;

use App\Modules\Product\Dto\ProductDto;
use App\Modules\Product\Query\IProductQuery;
use App\Shared\Assert;
use App\Shared\Doctrine\Entity\IEntity;
use App\Shared\Doctrine\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Uid\Uuid;

final class ProductEntityRepository extends EntityRepository implements IProductQuery
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, new ClassMetadata(Product::class));
    }

    public function findOneById(string $id): ?ProductDto
    {
        $product = $this->find($id);

        if($product instanceof Product) {
            return $this->convertEntityToDto($product);
        }

        return null;
    }

    public function findOneByName(string $name): ?ProductDto
    {
        $product = $this->findOneBy(['name' => $name]);

        if($product instanceof Product) {
            return $this->convertEntityToDto($product);
        }

        return null;
    }

    public function save(Product|IEntity $entity): void
    {
        Assert::isInstanceOf($entity, Product::class);
        parent::save($entity);
    }

    private function convertDtoToEntity(ProductDto $dto): Product
    {
        return new Product(
            Uuid::fromString($dto->getId()),
            $dto->getName(),
            $dto->getPrice(),
            $dto->getCurrencyCode()
        );
    }

    private function convertEntityToDto(Product $entity): ProductDto
    {
        return new ProductDto(
            $entity->getId()->toString(),
            $entity->getName(),
            $entity->getPrice(),
            $entity->getCurrency()->getCode()
        );
    }
}