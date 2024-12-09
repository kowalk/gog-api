<?php

namespace App\Tests\Modules\Product\Command\Validator;

use App\Modules\Common\Dto\CurrencyDto;
use App\Modules\Common\Query\ICurrencyQuery;
use App\Modules\Product\Command\AddProductCommand;
use App\Modules\Product\Command\Validator\AddProductValidator;
use App\Modules\Product\Dto\ProductDto;
use App\Modules\Product\Query\IProductQuery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class AddProductValidatorTest extends TestCase
{
    private $productRepository;
    private $currencyRepository;
    private $validator;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(IProductQuery::class);
        $this->currencyRepository = $this->createMock(ICurrencyQuery::class);
        $this->validator = new AddProductValidator($this->productRepository, $this->currencyRepository);
    }

    public function testValidateValidCommand()
    {
        $command = new AddProductCommand(Uuid::v4()->toString(),'Test Product', (float) 100, 'USD');

        $this->productRepository->method('findProductByName')->willReturn(null);
        $this->currencyRepository->method('findByCode')->willReturn(new CurrencyDto('USD', 'United States Dollar'));

        $this->validator->validate($command);

        $this->assertTrue(true); // If no exception is thrown, the test passes
    }

    public function testValidateProductAlreadyExists()
    {
        $command = new AddProductCommand(Uuid::v4()->toString(), 'Test Product', (float) 100, 'USD');

        $this->productRepository->method('findProductByName')->willReturn(new ProductDto(Uuid::v4()->toString(), 'Test Product', (float) 100, 'USD'));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Product with this name already exists');

        $this->validator->validate($command);
    }

    public function testValidatePriceIsZeroOrNegative()
    {
        $command = new AddProductCommand(Uuid::v4()->toString(), 'Test Product', (float) 0, 'USD');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Price must be greater than 0');

        $this->validator->validate($command);
    }

    public function testValidateCurrencyDoesNotExist()
    {
        $command = new AddProductCommand(Uuid::v4()->toString(), 'Test Product', (float) 100, 'INVALID');

        $this->currencyRepository->method('findByCode')->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Currency with this code does not exist');

        $this->validator->validate($command);
    }
}