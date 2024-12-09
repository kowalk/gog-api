<?php

namespace App\Modules\Cart\Command\Handler;

use App\Modules\Cart\Command\CreateCartCommand;
use App\Modules\Cart\Command\Validator\CreateCartValidator;
use App\Modules\Cart\Dto\CartDto;
use App\Modules\Cart\Service\ICartService;
use App\Modules\Common\Dto\IProductDto;
use App\Shared\Doctrine\Collection\DtoCollection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateCartCommandHandler
{
    private CreateCartValidator $validator;
    private ICartService $cartService;

    public function __construct(CreateCartValidator $validator, ICartService $cartService)
    {
        $this->validator = $validator;
        $this->cartService = $cartService;
    }

    public function __invoke(CreateCartCommand $command): void
    {
        $this->validator->validate($command);
        $this->cartService->save(
            new CartDto(
                $command->getCartId(),
                new DtoCollection([],[IProductDto::class])
            )
        );
    }
}