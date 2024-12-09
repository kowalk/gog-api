<?php

namespace App\Modules\Product\Command\Validator;

use App\Modules\Common\Command\ICommand;
use App\Modules\Common\Command\Validator\IValidator;
use App\Modules\Product\Command\UpdateProductCommand;
use App\Modules\Product\Query\IProductQuery;
use App\Shared\Assert;

final class UpdateProductValidator implements IValidator
{
    private IProductQuery $query;

    public function __construct(IProductQuery $query)
    {
        $this->query = $query;
    }

    public function validate(UpdateProductCommand|ICommand $command): void
    {
        Assert::isInstanceOf($command, UpdateProductCommand::class);

        $product = $this->query->findProductById($command->getId());

        if ($product === null) {
            throw new \InvalidArgumentException('Product not found');
        }

        if ($command->getName() === null && $command->getPrice() === null) {
            throw new \InvalidArgumentException('At least one of name or price must be provided');
        }

        if ($command->getPrice() !== null && $command->getPrice() <= 0) {
            throw new \InvalidArgumentException('Price must be greater than 0');
        }
    }
}