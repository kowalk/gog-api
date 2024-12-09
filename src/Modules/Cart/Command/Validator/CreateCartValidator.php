<?php

namespace App\Modules\Cart\Command\Validator;

use App\Modules\Cart\Command\CreateCartCommand;
use App\Modules\Cart\Query\ICartQuery;
use App\Modules\Common\Command\ICommand;
use App\Modules\Common\Command\Validator\IValidator;
use App\Shared\Assert;

final class CreateCartValidator implements IValidator
{
    private ICartQuery $cartQuery;

    public function __construct(ICartQuery $cartQuery)
    {
        $this->cartQuery = $cartQuery;
    }

    public function validate(CreateCartCommand|ICommand $command): void
    {
        Assert::isInstanceOf($command, CreateCartCommand::class);

        $cart = $this->cartQuery->findCartById($command->getCartId());
        if ($cart !== null) {
            throw new \InvalidArgumentException('Cart with this ID already exists');
        }
    }
}