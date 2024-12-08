<?php

namespace App\UserInterface\Rest\Controller\Product;

use App\Modules\Common\Bus\ICommandBus;
use App\Modules\Product\Command\UpdateProductCommand;
use App\Modules\Product\Command\Validator\UpdateProductValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class UpdateProductController extends AbstractController
{
    public function __construct(
        private readonly ICommandBus            $commandBus,
        private readonly UpdateProductValidator $validator
    ) {
    }

    #[Route('/product/{id}', methods: ['PATCH'])]
    public function patch(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'] ?? null;
        $price = $data['price'] ?? null;

        $command = new UpdateProductCommand($id, $name, $price);

        try {
            $this->validator->validate($command);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->commandBus->dispatch($command);
        return new JsonResponse(['status' => 'Product updated successfully']);
    }
}