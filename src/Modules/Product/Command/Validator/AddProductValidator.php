<?php

namespace App\Modules\Product\Command\Validator;

use App\Modules\Common\Command\ICommand;
use App\Modules\Common\Command\Validator\IValidator;
use App\Modules\Common\Query\ICurrencyQuery;
use App\Modules\Product\Command\AddProductCommand;
use App\Modules\Product\Query\IProductQuery;
use App\Shared\Assert;

final readonly class AddProductValidator implements IValidator
{
    public function __construct(
        private IProductQuery  $productQuery,
        private ICurrencyQuery $currencyQuery,
    )
    {
    }

    public function validate(AddProductCommand|ICommand $command): void
    {
        Assert::isInstanceOf($command, AddProductCommand::class);

        $product = $this->productQuery->findProductByName($command->getName());
        if($product !== null) {
            throw new \InvalidArgumentException('Product with this name already exists');
        }

        if($command->getPrice() <= 0) {
            throw new \InvalidArgumentException('Price must be greater than 0');
        }

        $currency = $this->currencyQuery->findByCode($command->getCurrencyCode());
        if($currency === null) {
            throw new \InvalidArgumentException('Currency with this code does not exist');
        }
    }
}