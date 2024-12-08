<?php
namespace App\Modules\Common\Query;

use App\Modules\Common\Dto\CurrencyDto;

interface ICurrencyQuery
{
    public function findByCode(string $code): ?CurrencyDto;
    public function getByCode(string $code): ?CurrencyDto;
}
