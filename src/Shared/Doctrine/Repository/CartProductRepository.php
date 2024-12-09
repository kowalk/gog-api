<?php

namespace App\Shared\Doctrine\Repository;

use App\Shared\Doctrine\Entity\CartProduct;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

final class CartProductRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, new ClassMetadata(CartProduct::class));
    }
}