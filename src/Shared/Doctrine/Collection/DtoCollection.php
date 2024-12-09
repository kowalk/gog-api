<?php
namespace App\Shared\Doctrine\Collection;

use App\Modules\Common\Dto\IDto;
use App\Modules\Common\Dto\IDtoCollection;
use App\Shared\Assert;
use Doctrine\Common\Collections\ArrayCollection;

final class DtoCollection extends ArrayCollection implements IDtoCollection
{
    public function __construct(
        array $elements = [],
        private readonly array $allowedElementClasses = []
    ) {
        Assert::allIsInstanceOf($elements, IDto::class);

        if(count($this->allowedElementClasses) > 0) {
            Assert::allIsInstanceOfAny($elements, $this->allowedElementClasses);
        }

        parent::__construct($elements);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->toArray());
    }

    public function add(mixed $element): void
    {
        $this->checkIsElementAllowed($element);
        parent::add($element);
    }

    public function removeElement(mixed $element): void
    {
        $this->checkIsElementAllowed($element);
        parent::removeElement($element);
    }

    public function count(): int
    {
        return parent::count();
    }

    public function toArray(): array
    {
        return parent::toArray();
    }

    private function checkIsElementAllowed(IDto $element): void
    {
        if($this->allowedElementClasses) {
            Assert::isInstanceOfAny($element, $this->allowedElementClasses);
        }
    }
}