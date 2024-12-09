<?php

namespace App\Shared\Doctrine\Repository;

use App\Shared\Assert;
use App\Shared\Doctrine\Entity\IEntity;
use App\Shared\Doctrine\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Types\UuidType;

final class ProductRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, new ClassMetadata(Product::class));
    }

    public function save(Product|IEntity $entity): void
    {
        Assert::isInstanceOf($entity, Product::class);
        parent::save($entity);
    }

    public function findAllByIds(array $ids): array
    {
        //convert ids to binary
        $ids = array_map(
            fn($id) => $this->getEntityManager()
                ->getConnection()
                ->convertToDatabaseValue($id, UuidType::NAME),
            $ids
        );

        return $this->findBy(['id' => $ids]);
    }

    public function getById(string $id): Product
    {
        $product = $this->find($id);

        if (!$product) {
            throw new \RuntimeException('Product not found');
        }

        return $product;
    }
}