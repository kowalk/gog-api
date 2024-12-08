<?php
namespace App\Modules\Common\Service;

use App\Modules\Common\Dto\IDto;

interface IPersistenceService
{
    public function save(IDto $dto): void;
    public function delete(IDto $dto): void;
}