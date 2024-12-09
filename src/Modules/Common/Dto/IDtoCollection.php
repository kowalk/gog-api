<?php

namespace App\Modules\Common\Dto;

interface IDtoCollection
{
    public function add(IDto $element): void;
    public function removeElement(IDto $element): void;
    public function getIterator(): \ArrayIterator;
    public function count(): int;
    /**
     * @return array<IDto>
     */
    public function toArray(): array;
}