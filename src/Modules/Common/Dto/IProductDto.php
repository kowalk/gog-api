<?php

namespace App\Modules\Common\Dto;

interface IProductDto extends IDto
{
    public function getName(): string;
    public function setName(string $name): void;
    public function getPrice(): float;
    public function setPrice(float $price): void;
    public function getCurrencyCode(): string;
    public function setCurrencyCode(string $currencyCode): void;
}