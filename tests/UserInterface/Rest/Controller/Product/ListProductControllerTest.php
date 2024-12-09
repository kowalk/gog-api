<?php

namespace App\Tests\UserInterface\Rest\Controller\Product;

use App\Modules\Product\Query\IProductQuery;
use App\Shared\Doctrine\Entity\Currency;
use App\Shared\Doctrine\Entity\Product;
use App\Shared\Doctrine\Repository\ProductEntityRepository;
use App\Shared\Doctrine\Repository\ProductRepository;
use App\UserInterface\Rest\Controller\Product\ListProductController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

final class ListProductControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;
    private IProductQuery $productQuery;
    private ProductRepository $productRepository;
    private ListProductController $controller;
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $this->productQuery = $this->client->getContainer()->get(IProductQuery::class);
        $this->productRepository = new ProductRepository($this->entityManager);
        $this->controller = new ListProductController($this->productQuery);

        $currency = $this->entityManager->getRepository(Currency::class)->findOneBy(['code' => 'USD']);

        // Clear the product table
        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('DELETE FROM product');

        $this->addTestProducts($currency);
    }

    private function addTestProducts(Currency $currency): void
    {
        $products = [
            new Product(Uuid::v4(), 'Fallout', 1.99, $currency),
            new Product(Uuid::v4(), 'Don’t Starve', 2.99, $currency),
            new Product(Uuid::v4(), 'Baldur’s Gate', 3.99, $currency),
            new Product(Uuid::v4(), 'Icewind Dale', 4.99, $currency),
            new Product(Uuid::v4(), 'Bloodborne', 5.99, $currency),
        ];

        foreach ($products as $product) {
            $this->productRepository->save($product);
        }
    }

    public function testReturnsProductsForValidRequest()
    {
        $request = new Request(['page' => 1, 'limit' => 2]);
        $response = $this->controller->list($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertCount(2, json_decode($response->getContent(), true));
    }

    public function testReturnsDefaultLimitWhenLimitIsInvalid()
    {
        $request = new Request(['page' => 1, 'limit' => 5]);
        $response = $this->controller->list($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertCount(3, json_decode($response->getContent(), true));
    }

    public function testReturnsDefaultLimitWhenLimitIsNegative()
    {
        $request = new Request(['page' => 1, 'limit' => -1]);
        $response = $this->controller->list($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertCount(3, json_decode($response->getContent(), true));
    }

    public function testReturnsProductsForValidPageAndLimit()
    {
        $request = new Request(['page' => 2, 'limit' => 2]);
        $response = $this->controller->list($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertCount(2, json_decode($response->getContent(), true));
    }
}