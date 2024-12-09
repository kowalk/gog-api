<?php

namespace App\UserInterface\Rest\Controller\Cart;

use App\Modules\Cart\Command\AddProductToCartCommand;
use App\Modules\Cart\Command\Validator\AddProductToCartValidator;
use App\Modules\Common\Bus\ICommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

final class AddProductToCartController extends AbstractController
{
    public function __construct(
        private readonly ICommandBus $commandBus,
        private readonly AddProductToCartValidator $validator,
    ) {
    }

    #[Route('/cart/{cartId}/product', methods: ['POST'])]
    public function addProduct(Request $request, string $cartId): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $productId = $data['productId'] ?? null;
        $quantity = $data['quantity'] ?? null;

        if (!$productId) {
            return new JsonResponse(['error' => 'Product ID is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!Uuid::isValid($productId)) {
            return new JsonResponse(['error' => 'Valid Product ID is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!Uuid::isValid($cartId)) {
            return new JsonResponse(['error' => 'Valid Cart ID is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $command = new AddProductToCartCommand($cartId, $productId, $quantity);

        try {
            $this->validator->validate($command);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->commandBus->dispatch($command);

        return new JsonResponse(['status' => 'Product added to cart', 'cartId' => $cartId, 'productId' => $productId], JsonResponse::HTTP_OK);
    }
}