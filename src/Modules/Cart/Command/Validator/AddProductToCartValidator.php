<?php

namespace App\Modules\Cart\Command\Validator;

use App\Modules\Cart\Command\AddProductToCartCommand;
use App\Modules\Cart\Query\ICartQuery;
use App\Modules\Common\Command\ICommand;
use App\Modules\Common\Command\Validator\IValidator;
use App\Modules\Product\Query\IProductQuery;
use App\Shared\Assert;
use InvalidArgumentException;

final class AddProductToCartValidator implements IValidator
{
    private ICartQuery $cartQuery;
    private IProductQuery $productQuery;

    public function __construct(ICartQuery $cartQuery, IProductQuery $productQuery)
    {
        $this->cartQuery = $cartQuery;
        $this->productQuery = $productQuery;
    }

    public function validate(AddProductToCartCommand|ICommand $command): void
    {
        Assert::isInstanceOf($command, AddProductToCartCommand::class);

        $cart = $this->cartQuery->findCartById($command->getCartId());

        if (!$cart) {
            throw new InvalidArgumentException("Cart with ID " . $command->getCartId() . " not found.");
        }

        $product = $this->productQuery->findProductById($command->getProductId());

        if (!$product) {
            throw new InvalidArgumentException("Product with ID " . $command->getProductId() . " not found.");
        }

        //maximum quantity of same product in cart is 10
        $currentProductQuantity = $cart->getProducts()->getProduct($command->getProductId())?->getQuantity() ?? 0;
        $newQuantity = $currentProductQuantity + intval($command->getQuantity() ?? 1);

        if ($newQuantity > 10) {
            throw new InvalidArgumentException("Maximum quantity of product with ID " . $command->getProductId() . " already in cart.");
        }
    }
}