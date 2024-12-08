<?php

namespace App\Modules\Product\Command;

final class UpdateProductCommand
{
    private string $id;
    private ?string $name;
    private ?float $price;

    public function __construct(string $id, ?string $name, ?float $price)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }
}