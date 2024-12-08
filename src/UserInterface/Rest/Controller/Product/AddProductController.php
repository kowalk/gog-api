<?php

namespace App\UserInterface\Rest\Controller\Product;

use App\Modules\Common\Bus\ICommandBus;
use App\Modules\Product\Command\AddProductCommand;
use App\Modules\Product\Command\Validator\AddProductValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

final class AddProductController extends AbstractController
{
    public function __construct(
        private readonly ICommandBus $commandBus,
        private readonly AddProductValidator $validator
    ) {

    }

    #[Route('/product/add', name: 'add_product', methods: ['POST'])]
    public function addProduct(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $command = new AddProductCommand(
            Uuid::v4()->toString(),
            $data['name'],
            $data['price'],
            $data['currency']
        );

        try {
            $this->validator->validate($command);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $this->commandBus->dispatch($command);

        return new JsonResponse(['status' => 'Product added successfully'], Response::HTTP_CREATED);
    }
}