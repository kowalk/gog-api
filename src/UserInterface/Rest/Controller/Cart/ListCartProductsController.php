<?php

namespace App\UserInterface\Rest\Controller\Cart;

use App\Modules\Cart\Dto\CartProductDto;
use App\Modules\Cart\Query\ICartQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

final class ListCartProductsController extends AbstractController
{
    public function __construct(
        private readonly ICartQuery $cartQuery
    ) {
    }

    #[Route('/cart/{cartId}/product', methods: ['GET'])]
    public function listProducts(string $cartId): JsonResponse
    {
        if (!Uuid::isValid($cartId)) {
            return new JsonResponse(['error' => 'Valid Cart ID is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $cart = $this->cartQuery->findCartById($cartId);
        if (!$cart) {
            return new JsonResponse(['error' => 'Cart not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $products = array_map(function (CartProductDto $cartProduct) {
            return [
                'productId' => $cartProduct->getProductId(),
                'quantity' => $cartProduct->getQuantity(),
            ];
        }, $cart->getProducts()->toArray());

        return new JsonResponse(['cartId' => $cartId, 'products' => array_values($products)], JsonResponse::HTTP_OK);
    }
}