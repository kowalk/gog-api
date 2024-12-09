<?php

namespace App\Modules\Product\Command\Handler;

use App\Modules\Product\Command\UpdateProductCommand;
use App\Modules\Product\Command\Validator\UpdateProductValidator;
use App\Modules\Product\Query\IProductQuery;
use App\Modules\Product\Service\IProductService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateProductHandler
{
    public function __construct(
        private UpdateProductValidator $validator,
        private IProductService        $productService,
        private IProductQuery          $productQuery
    ) {

    }

    public function __invoke(UpdateProductCommand $command): void
    {
        $this->validator->validate($command);

        $product = $this->productQuery->getProductById($command->getId());

        if ($command->getName() !== null) {
            $product->setName($command->getName());
        }

        if ($command->getPrice() !== null) {
            $product->setPrice($command->getPrice());
        }

        $this->productService->save($product);
    }
}