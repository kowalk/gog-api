<?php

namespace App\Tests\UserInterface\Rest\Controller\Cart;

use App\Modules\Cart\Dto\CartProductDto;
use App\Modules\Cart\Query\ICartQuery;
use App\Shared\Doctrine\Entity\Cart;
use App\Shared\Doctrine\Entity\Currency;
use App\Shared\Doctrine\Entity\Product;
use App\Shared\Doctrine\Repository\ProductRepository;
use App\UserInterface\Rest\Controller\Cart\ListCartProductsController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Uid\Uuid;

final class ListCartProductsControllerTest extends WebTestCase
{

    const PROD_1_ID = 'e15e22ec-c5df-4fb1-a3a2-2d69ad5995c0';
    const PROD_2_ID = 'cab35972-1428-46c4-87a8-131b2f6348c4';
    const PROD_3_ID = '82a43dba-4b5d-47bd-897a-07c81a03933c';
    const PROD_4_ID = '1414010d-6bb1-42ad-a650-47f4981205b4';
    const PROD_5_ID = '67ec51cb-f553-4b4d-96a5-b71a76829a08';

    private EntityManagerInterface $entityManager;
    private ICartQuery $cartQuery;
    private ListCartProductsController $controller;

    private ProductRepository $productRepository;
    private $client;
    private array $products = [];

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $this->cartQuery = $this->client->getContainer()->get(ICartQuery::class);
        $this->controller = new ListCartProductsController($this->cartQuery);

        $currency = $this->entityManager->getRepository(Currency::class)->findOneBy(['code' => 'USD']);
        $this->productRepository = $this->client->getContainer()->get(ProductRepository::class);

        // Clear the product table
        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('DELETE FROM cart_product');
        $connection->executeStatement('DELETE FROM cart');
        $connection->executeStatement('DELETE FROM product');

        $this->addTestProducts($currency);
    }

    private function addTestProducts(Currency $currency): void
    {
        $this->products = [
            new Product(Uuid::fromString(self::PROD_1_ID), 'Fallout', 1.99, $currency),
            new Product(Uuid::fromString(self::PROD_2_ID), 'Don’t Starve', 2.99, $currency),
            new Product(Uuid::fromString(self::PROD_3_ID), 'Baldur’s Gate', 3.99, $currency),
            new Product(Uuid::fromString(self::PROD_4_ID), 'Icewind Dale', 4.99, $currency),
            new Product(Uuid::fromString(self::PROD_5_ID), 'Bloodborne', 5.99, $currency),
        ];

        foreach ($this->products as $product) {
            $this->productRepository->save($product);
        }
    }

    public function testListProductsWithValidCartIdReturnsProducts()
    {
        $cartId = Uuid::v4();

        $cart = new Cart($cartId);
        $cart->addProduct($this->products[0], 1);
        $cart->addProduct($this->products[1], 5);
        $cart->addProduct($this->products[2], 8);

        $this->entityManager->persist($cart);

        $this->client->request('GET', '/api/cart/' . $cart->getId()->toString() . '/product', [], [], ['CONTENT_TYPE' => 'application/json']);

        $response = $this->client->getResponse();
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'cartId' => $cartId,
                'products' => [
                    ['productId' => $this->products[0]->getId()->toString(), 'quantity' => 1],
                    ['productId' => $this->products[1]->getId()->toString(), 'quantity' => 5],
                    ['productId' => $this->products[2]->getId()->toString(), 'quantity' => 8]
                ]
            ]),
            $response->getContent()
        );
    }

    public function testListProductsWithInvalidCartIdReturnsBadRequest()
    {
        $response = $this->controller->listProducts('invalid-cart-id');

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => 'Valid Cart ID is required']),
            $response->getContent()
        );
    }

    public function testListProductsWithNonExistentCartIdReturnsNotFound()
    {
        $cartId = Uuid::v4()->toRfc4122();

        $response = $this->controller->listProducts($cartId);

        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => 'Cart not found']),
            $response->getContent()
        );
    }
}