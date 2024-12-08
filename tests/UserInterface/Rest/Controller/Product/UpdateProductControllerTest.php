<?php

namespace App\Tests\UserInterface\Rest\Controller\Product;

use App\Shared\Doctrine\Entity\Currency;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Shared\Doctrine\Entity\Product;
use Symfony\Component\Uid\Uuid;

class UpdateProductControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        // Clear the product table
        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('DELETE FROM `product`');

        $currency = $this->entityManager->getRepository(Currency::class)->findOneBy(['code' => 'USD']);

        // Insert example product
        $product = new Product(
            Uuid::v4(),
            'Example Product',
            100.0,
            $currency
        );

        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function testValidateValidCommand()
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['name' => 'Example Product']);
        $this->client->request('PATCH', '/product/' . $product->getId(), [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'Updated Product',
            'price' => 150
        ]));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['status' => 'Product updated successfully']), $this->client->getResponse()->getContent());

        // Check if the product was updated in the database
        $updatedProduct = $this->entityManager->getRepository(Product::class)->find($product->getId());
        $this->assertEquals('Updated Product', $updatedProduct->getName());
        $this->assertEquals(150, $updatedProduct->getPrice());
    }

    public function testInvalidCommandThrowsException()
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['name' => 'Example Product']);
        $this->client->request('PATCH', '/product/' . $product->getId(), [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'Updated Product',
            'price' => -150
        ]));

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['error' => 'Price must be greater than 0']), $this->client->getResponse()->getContent());
    }

    public function testNoDataToUpdate()
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['name' => 'Example Product']);
        $this->client->request('PATCH', '/product/' . $product->getId(), [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([]));

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['error' => 'At least one of name or price must be provided']), $this->client->getResponse()->getContent());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}