<?php

namespace App\Tests\UserInterface\Rest\Controller\Cart;

use App\Shared\Doctrine\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

final class CreateCartControllerTest extends WebTestCase
{

    private EntityManagerInterface $entityManager;
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        // Clear the cart table
        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('DELETE FROM cart_product');
        $connection->executeStatement('DELETE FROM cart');
    }

    public function testCreateCartSuccessfully(): void
    {
        $this->client->request('POST', '/api/cart', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([]));

        $response = $this->client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        $this->assertArrayHasKey('cartId', json_decode($response->getContent(), true));
        $this->assertEquals('Cart created', json_decode($response->getContent(), true)['status']);

        //verify in database
        $cartId = json_decode($response->getContent(), true)['cartId'];
        $cart = $this->entityManager->getRepository(Cart::class)->findOneBy(['id' => $cartId]);
        $this->assertInstanceOf(Cart::class, $cart);

        $count = $this->entityManager->getRepository(Cart::class)->count([]);
        $this->assertEquals(1, $count);
    }
}