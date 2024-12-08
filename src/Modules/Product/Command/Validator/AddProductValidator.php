<?php

namespace App\Modules\Product\Command\Validator;

use App\Modules\Common\Command\ICommand;
use App\Modules\Common\Query\ICurrencyQuery;
use App\Modules\Product\Command\AddProductCommand;
use App\Modules\Product\Query\IProductQuery;
use App\Shared\Assert;

final readonly class AddProductValidator implements IValidator
{
    public function __construct(
        private IProductQuery  $productRepository,
        private ICurrencyQuery $currencyRepository,
    )
    {

    }

    public function validate(AddProductCommand|ICommand $command): void
    {
        Assert::isInstanceOf($command, AddProductCommand::class);

        $product = $this->productRepository->findOneByName($command->getName());
        if($product !== null) {
            throw new \InvalidArgumentException('Product with this name already exists');
        }

        if($command->getPrice() <= 0) {
            throw new \InvalidArgumentException('Price must be greater than 0');
        }

        $currency = $this->currencyRepository->findByCode($command->getCurrencyCode());
        if($currency === null) {
            throw new \InvalidArgumentException('Currency with this code does not exist');
        }
    }
}