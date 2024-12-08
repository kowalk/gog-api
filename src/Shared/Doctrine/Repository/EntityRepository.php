<?php

namespace App\Shared\Doctrine\Repository;

use App\Shared\Assert;
use App\Shared\Doctrine\Entity\IEntity;
use Doctrine\ORM\EntityRepository as ORMEntityRepository;

abstract class EntityRepository extends ORMEntityRepository
{
    public function save(IEntity $entity): void
    {
        Assert::isInstanceOf($entity, IEntity::class);

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function delete(IEntity $entity): void
    {
        Assert::isInstanceOf($entity, IEntity::class);

        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }
}