<?php

namespace App\Shared\Query;

use App\Modules\Common\Dto\CurrencyDto;
use App\Modules\Common\Query\ICurrencyQuery;
use App\Shared\Doctrine\Entity\Currency;
use App\Shared\Doctrine\Entity\IEntity;
use App\Shared\Doctrine\Repository\CurrencyRepository;

final class CurrencyQuery implements ICurrencyQuery, IDoctrineQuery
{
    public function __construct(private readonly CurrencyRepository $currencyRepository)
    {
    }

    public function getByCode(string $code): CurrencyDto
    {
        $currency = $this->findByCode($code);

        if($currency === null) {
            throw new \Exception('Currency not found');
        }

        return $currency;
    }

    public function findByCode(string $code): ?CurrencyDto
    {
        $currency = $this->currencyRepository->findOneBy(['code' => $code]);

        if($currency === null) {
            return null;
        }

        return $this->convertEntityToDto($currency);
    }


    public function convertEntityToDto(IEntity|Currency $entity): CurrencyDto
    {
        if(!$entity instanceof Currency) {
            throw new \RuntimeException('Invalid entity');
        }

        return new CurrencyDto(
            $entity->getCode(),
            $entity->getName()
        );
    }
}