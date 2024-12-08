<?php

namespace App\Modules\Common\Dto;

final readonly class CurrencyDto implements IDto
{
    public function __construct(
        private string $code,
        private string $name,
    ) {}

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }
}