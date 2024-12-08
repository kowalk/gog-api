<?php

namespace App\Tests\UserInterface\Rest\Controller\Product;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Shared\Doctrine\Entity\Product;

class AddProductControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        // Clear the product table
        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('DELETE FROM product');
    }

    public function testAddProductValidationFails()
    {
        $this->client->request('POST', '/product/add', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'Test Product',
            'price' => 100,
            'currency' => 'INVALID_CURRENCY'
        ]));

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['error' => 'Currency with this code does not exist']), $this->client->getResponse()->getContent());
    }

    public function testAddProductValidationSucceeds()
    {
        $this->client->request('POST', '/product/add', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'Test Product',
            'price' => 100,
            'currency' => 'USD'
        ]));

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['status' => 'Product added successfully']), $this->client->getResponse()->getContent());

        // Check if the product was added to the database
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['name' => 'Test Product']);
        $this->assertNotNull($product);
        $this->assertEquals(100, $product->getPrice());
        $this->assertEquals('USD', $product->getCurrency()->getCode());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}