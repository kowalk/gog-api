<?php

namespace App\Modules\Product\Command;

use App\Shared\Assert;

class AddProductCommand
{
    private string $id;
    private string $name;
    private float $price;
    private string $currencyCode;

    public function __construct(string $id, string $name, float $price, string $currencyCode)
    {
        Assert::uuid($id);
        Assert::notEmpty($name);
        Assert::greaterThanEq($price, 0);
        Assert::notEmpty($currencyCode);

        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->currencyCode = $currencyCode;
    }

    public function getId(): string
    {
        return $this->id;
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
}