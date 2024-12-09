<?php

namespace App\Modules\Cart\Command\Handler;

use App\Modules\Cart\Command\AddProductToCartCommand;
use App\Modules\Cart\Command\Validator\AddProductToCartValidator;
use App\Modules\Cart\Query\ICartQuery;
use App\Shared\Doctrine\Service\CartEntityService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class AddProductToCartHandler
{
    private ICartQuery $cartQuery;
    private CartEntityService $cartService;
    private AddProductToCartValidator $validator;

    public function __construct(
        ICartQuery $cartQuery,
        CartEntityService $cartService,
        AddProductToCartValidator $validator
    ) {
        $this->cartQuery = $cartQuery;
        $this->cartService = $cartService;
        $this->validator = $validator;
    }

    public function __invoke(AddProductToCartCommand $command): void
    {
        $this->validator->validate($command);

        $cartDto = $this->cartQuery->getCartById($command->getCartId());
        $cartDto->addProduct($command->getProductId(), $command->getQuantity());

        $this->cartService->save($cartDto);
    }
}