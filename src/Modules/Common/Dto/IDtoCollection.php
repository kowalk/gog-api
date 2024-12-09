<?php

namespace App\Modules\Common\Dto;

interface IDtoCollection
{
    /**
     * @param array<IDto> $elements
     * @param array<string> $allowedElementClasses
     */
    public function __construct(array $elements = [], array $allowedElementClasses = []);
    public function add(IDto $element): void;
    public function removeElement(IDto $element): void;
    public function getIterator(): \ArrayIterator;
    public function count(): int;
    /**
     * @return array<IDto>
     */
    public function toArray(): array;
}