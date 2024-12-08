<?php

namespace App\Modules\Product\Query;

use App\Modules\Product\Dto\ProductDto;

interface IProductQuery
{
    public function findOneById(string $id): ?ProductDto;
    public function findOneByName(string $name): ?ProductDto;
}