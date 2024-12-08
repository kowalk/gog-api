<?php

namespace App\Shared\Doctrine\Repository;

use App\Modules\Common\Dto\CurrencyDto;
use App\Modules\Common\Query\ICurrencyQuery;
use App\Shared\Doctrine\Entity\Currency;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

final class CurrencyEntityRepository extends EntityRepository implements ICurrencyQuery
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, new ClassMetadata(Currency::class));
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
        $currency = $this->findOneBy(['code' => $code]);

        if($currency === null) {
            return null;
        }

        return $this->convertEntityToDto($currency);
    }


    public function convertEntityToDto(Currency $entity): CurrencyDto
    {
        return new CurrencyDto(
            $entity->getCode(),
            $entity->getName()
        );
    }
}