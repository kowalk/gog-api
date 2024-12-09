<?php

namespace App\Shared\Doctrine\Repository;

use App\Shared\Assert;
use App\Shared\Doctrine\Entity\IEntity;
use App\Shared\Doctrine\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

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
        $products = $this->findBy(['id' => $ids]);

        return $products;
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