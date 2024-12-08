<?php

namespace App\Shared\Doctrine\Service;

use App\Modules\Common\Dto\IDto;
use App\Shared\Doctrine\Entity\IEntity;

abstract class EntityService
{
    public abstract function convertToEntity(IDto $dto): IEntity;
}