<?php

namespace App\Tests\UserInterface\Rest\Controller\Cart;

use App\Shared\Doctrine\Entity\Currency;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Shared\Doctrine\Entity\Cart;
use App\Shared\Doctrine\Entity\Product;
use Symfony\Component\Uid\Uuid;

final class AddProductToCartControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        // Clear the cart and product tables
        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('DELETE FROM cart_product');
        $connection->executeStatement('DELETE FROM product');
        $connection->executeStatement('DELETE FROM cart');
    }

    private function createCart(): Cart
    {
        $cart = new Cart(Uuid::v4());
        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        return $cart;
    }

    public function testProductIsAddedToCartSuccessfully()
    {
        $cart = $this->createCart();

        $currency = $this->entityManager->getRepository(Currency::class)->findOneBy(['code' => 'USD']);
        $product = new Product(Uuid::v4(), 'Test Product', 100.0, $currency);
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $this->client->request('POST', '/api/cart/' . $cart->getId() . '/product', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['productId' => $product->getId()]));

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertStringContainsString('Product added to cart', $response->getContent());

        // Verify the product was added to the cart
        $cart = $this->entityManager->getRepository(Cart::class)->find($cart->getId());
        $this->assertCount(1, $cart->getCartProducts());

        //verify product quantity
        $cartProduct = $cart->getCartProducts()->first();
        $this->assertEquals(1, $cartProduct->getQuantity());
        $this->assertEquals($product->getId(), $cartProduct->getProduct()->getId());
    }

    public function testAddMultipleTimesSameProductsToCart()
    {
        $cart = $this->createCart();

        $currency = $this->entityManager->getRepository(Currency::class)->findOneBy(['code' => 'USD']);
        $product = new Product(Uuid::v4(), 'Test Product', 100.0, $currency);
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        // Add the same product twice
        $this->client->request('POST', '/api/cart/' . $cart->getId() . '/product', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['productId' => $product->getId()]));
        $this->client->request('POST', '/api/cart/' . $cart->getId() . '/product', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['productId' => $product->getId()]));

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertStringContainsString('Product added to cart', $response->getContent());

        // Verify the product was added to the cart twice
        $cart = $this->entityManager->getRepository(Cart::class)->find($cart->getId());
        $this->assertCount(1, $cart->getCartProducts());

        // Verify product quantity
        $cartProduct = $cart->getCartProducts()->first();
        $this->assertEquals(2, $cartProduct->getQuantity());
        $this->assertEquals($product->getId(), $cartProduct->getProduct()->getId());
    }

    public function testMaximumProductQuantityInCart()
    {
        $cart = $this->createCart();

        $currency = $this->entityManager->getRepository(Currency::class)->findOneBy(['code' => 'USD']);
        $product = new Product(Uuid::v4(), 'Test Product', 100.0, $currency);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
        $maxQuantity = 10; // Define the maximum quantity allowed

        //add maximum quantity of product to cart
        $this->client->request('POST', '/api/cart/' . $cart->getId() . '/product', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['productId' => $product->getId(), 'quantity' => $maxQuantity]));

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertStringContainsString('Product added to cart', $response->getContent());

        // Verify the product quantity does not exceed the maximum limit
        $cart = $this->entityManager->getRepository(Cart::class)->find($cart->getId());
        $this->assertCount(1, $cart->getCartProducts());

        $cartProduct = $cart->getCartProducts()->first();
        $this->assertEquals($maxQuantity, $cartProduct->getQuantity());
        $this->assertEquals($product->getId(), $cartProduct->getProduct()->getId());

        //add one more product to cart
        $this->client->request('POST', '/api/cart/' . $cart->getId() . '/product', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['productId' => $product->getId()]));
        $response = $this->client->getResponse();

        // Verify the product quantity does not exceed the maximum limit
        $cart = $this->entityManager->getRepository(Cart::class)->find($cart->getId());
        $this->assertCount(1, $cart->getCartProducts());

        $cartProduct = $cart->getCartProducts()->first();
        $this->assertEquals($maxQuantity, $cartProduct->getQuantity());
        $this->assertEquals($product->getId(), $cartProduct->getProduct()->getId());

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['error' => 'Maximum quantity of product with ID '. $product->getId() .' already in cart.']), $response->getContent());
    }

    public function testAddProductWithExceededQuantity()
    {
        $cart = $this->createCart();

        $currency = $this->entityManager->getRepository(Currency::class)->findOneBy(['code' => 'USD']);
        $product = new Product(Uuid::v4(), 'Test Product', 100.0, $currency);
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $quantity = 1000;

        $this->client->request('POST', '/api/cart/' . $cart->getId() . '/product', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['productId' => $product->getId(), 'quantity' => $quantity]));

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['error' => 'Maximum quantity of product with ID '. $product->getId() .' already in cart.']), $response->getContent());
    }


    public function testProductIdIsRequired()
    {
        $cart = $this->createCart();

        $this->client->request('POST', '/api/cart/' . $cart->getId() . '/product', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([]));

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString('Product ID is required', $response->getContent());
    }

    public function testCartWithInvalidIdFormatNotFound()
    {
        $this->client->request('POST', '/api/cart/invalid-cart-id/product', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['productId' => '123']));

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString('Valid Product ID is required', $response->getContent());
    }

    public function testCartNotFound()
    {
        $cartId = Uuid::v4()->toString();
        $this->client->request('POST', '/api/cart/'. $cartId .'/product', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['productId' => Uuid::v4()->toString()]));

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString('Cart with ID '. $cartId .' not found.', $response->getContent());
    }

    public function testProductWithInvalidIdFormatNotFound()
    {
        $cart = $this->createCart();

        $this->client->request('POST', '/api/cart/' . $cart->getId() . '/product', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['productId' => 'invalid-product-id']));

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString('Valid Product ID is required', $response->getContent());
    }

    public function testProductNotFound()
    {
        $cart = $this->createCart();
        $productId = Uuid::v4()->toString();

        $this->client->request('POST', '/api/cart/' . $cart->getId() . '/product', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['productId' => $productId]));

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString('Product with ID '. $productId .' not found.', $response->getContent());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}