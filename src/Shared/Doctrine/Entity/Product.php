<?php

namespace App\Shared\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class Product implements IEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $name;

    #[ORM\Column(type: 'decimal', scale: 2)]
    private float $price;

    #[ORM\ManyToOne(targetEntity: Currency::class)]
    #[ORM\JoinColumn(referencedColumnName: 'code', nullable: false)]
    private Currency $currency;

    public function __construct(Uuid $id, string $name, float $price, Currency $currency)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->currency = $currency;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): self
    {
        $this->currency = $currency;
        return $this;
    }
}