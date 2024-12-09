<?php

namespace App\Modules\Product\Dto;

use App\Modules\Common\Dto\IDto;
use App\Modules\Common\Dto\IProductDto;
use App\Shared\Assert;

final class ProductDto implements IProductDto
{
    private string $id;
    private string $name;
    private float $price;
    private string $currencyCode;

    public function __construct(string $id, string $name, float $price, string $currencyCode)
    {
        Assert::uuid($id);
        Assert::notEmpty($name);
        Assert::maxLength($name, 255);
        Assert::greaterThan($price, 0);

        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->currencyCode = $currencyCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setCurrencyCode(string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }
}