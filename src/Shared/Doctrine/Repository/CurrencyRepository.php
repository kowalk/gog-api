<?php

namespace App\Shared\Doctrine\Repository;

use App\Shared\Doctrine\Entity\Currency;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

final class CurrencyRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, new ClassMetadata(Currency::class));
    }
}