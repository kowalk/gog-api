<?php

namespace App\UserInterface\Rest\Controller\Cart;

use App\Modules\Cart\Command\CreateCartCommand;
use App\Modules\Common\Bus\ICommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

final class CreateCartController extends AbstractController
{
    public function __construct(private ICommandBus $commandBus)
    {
    }

    #[Route('/cart', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $cartId = Uuid::v4()->toString();

        $command = new CreateCartCommand($cartId);
        $this->commandBus->dispatch($command);

        return new JsonResponse(['status' => 'Cart created', 'cartId' => $cartId], JsonResponse::HTTP_CREATED);
    }
}