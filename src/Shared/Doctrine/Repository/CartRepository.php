<?php

namespace App\Shared\Doctrine\Repository;

use App\Shared\Assert;
use App\Shared\Doctrine\Entity\Cart;
use App\Shared\Doctrine\Entity\IEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

final class CartRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, new ClassMetadata(Cart::class));
    }

    public function save(Cart|IEntity $entity): void
    {
        Assert::isInstanceOf($entity, Cart::class);
        parent::save($entity);
    }

    public function findAllByIds(array $ids): array
    {
        $carts = $this->findBy(['id' => $ids]);

        return $carts;
    }

    public function getById(string $id): Cart
    {
        $cart = $this->find($id);

        if (!$cart) {
            throw new \RuntimeException('Cart not found');
        }

        return $cart;
    }
}