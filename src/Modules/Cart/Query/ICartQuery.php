<?php

namespace App\Modules\Cart\Query;

use App\Modules\Cart\Dto\CartDto;

interface ICartQuery
{
    public function findCartById(string $cartId): ?CartDto;
    public function getCartById(string $cartId): CartDto;
    public function deleteCartById(string $cartId): void;
}