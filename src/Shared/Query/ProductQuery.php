<?php

namespace App\Shared\Query;

use App\Modules\Common\Dto\IDtoCollection;
use App\Modules\Product\Dto\ProductDto;
use App\Modules\Product\Query\IProductQuery;
use App\Shared\Assert;
use App\Shared\Doctrine\Collection\DtoCollection;
use App\Shared\Doctrine\Entity\IEntity;
use App\Shared\Doctrine\Entity\Product;
use App\Shared\Doctrine\Repository\ProductRepository;

final class ProductQuery implements IProductQuery, IDoctrineQuery
{
    public function __construct(
        private readonly ProductRepository $productRepository
    ) {
    }

    public function findProductById(string $id): ?ProductDto
    {
        $product = $this->productRepository->find($id);

        if($product instanceof Product) {
            return $this->convertEntityToDto($product);
        }

        return null;
    }

    public function findProductByName(string $name): ?ProductDto
    {
        $product = $this->productRepository->findOneBy(['name' => $name]);

        if($product instanceof Product) {
            return $this->convertEntityToDto($product);
        }

        return null;
    }

    public function findAllProducts($limit, $offset): IDtoCollection
    {
        $products = $this->productRepository->findBy([], null, $limit, $offset);
        $dtos = [];

        foreach ($products as $product) {
            $dtos[] = $this->convertEntityToDto($product);
        }

        return new DtoCollection($dtos, [ProductDto::class]);
    }

    public function getProductById(string $id): ProductDto
    {
        $product = $this->findProductById($id);

        if($product instanceof ProductDto) {
            return $product;
        }

        throw new \RuntimeException('Product not found');
    }

    public function convertEntityToDto(IEntity|Product $entity): ProductDto
    {
        Assert::isInstanceOf($entity, Product::class);

        return new ProductDto(
            $entity->getId()->toString(),
            $entity->getName(),
            $entity->getPrice(),
            $entity->getCurrency()->getCode()
        );
    }
}