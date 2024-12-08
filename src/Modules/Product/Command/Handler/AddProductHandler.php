<?php

namespace App\Modules\Product\Command\Handler;

use App\Modules\Product\Command\AddProductCommand;
use App\Modules\Product\Command\Validator\AddProductValidator;
use App\Modules\Product\Dto\ProductDto;
use App\Modules\Product\Service\IProductService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class AddProductHandler
{
    public function __construct(
        private IProductService            $productService,
        private AddProductValidator $validator
    ) {
    }

    public function __invoke(AddProductCommand $command): void
    {
        $this->validator->validate($command);

        $product = new ProductDto(
            $command->getId(),
            $command->getName(),
            $command->getPrice(),
            $command->getCurrencyCode()
        );

        $this->productService->save($product);
    }
}