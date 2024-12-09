<?php

namespace App\Shared\Query;


use App\Modules\Common\Dto\IDto;
use App\Shared\Doctrine\Entity\IEntity;

interface IDoctrineQuery
{
    public function convertEntityToDto(IEntity $entity): IDto;
}