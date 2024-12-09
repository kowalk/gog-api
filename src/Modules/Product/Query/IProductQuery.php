<?php

namespace App\Modules\Product\Query;

use App\Modules\Common\Dto\IDtoCollection;
use App\Modules\Product\Dto\ProductDto;

interface IProductQuery
{
    public function findProductById(string $id): ?ProductDto;
    public function findProductByName(string $name): ?ProductDto;
    public function findAllProducts($limit, $offset): IDtoCollection;
    public function getProductById(string $id): ProductDto;
}