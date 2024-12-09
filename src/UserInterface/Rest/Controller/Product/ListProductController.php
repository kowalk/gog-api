<?php

namespace App\UserInterface\Rest\Controller\Product;

use App\Modules\Product\Query\IProductQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class ListProductController extends AbstractController
{
    private const DEFAULT_LIMIT = 3;
    private const MAX_LIMIT = 3;

    public function __construct(private readonly IProductQuery $productQuery)
    {
    }

    #[Route('/product', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = (int) $request->query->get('limit', self::DEFAULT_LIMIT);

        if ($limit < 1 || $limit > self::MAX_LIMIT) {
            $limit = self::DEFAULT_LIMIT;
        }

        $offset = ($page - 1) * $limit;
        $products = $this->productQuery->findAllProducts($limit, $offset);

        return new JsonResponse($products->toArray());
    }
}