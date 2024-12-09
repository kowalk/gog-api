<?php

namespace App\Tests\Modules\Cart\Command\Validator;

use App\Modules\Cart\Command\CreateCartCommand;
use App\Modules\Cart\Command\Validator\CreateCartValidator;
use App\Modules\Cart\Dto\CartDto;
use App\Modules\Cart\Query\ICartQuery;
use App\Modules\Common\Command\ICommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class CreateCartValidatorTest extends TestCase
{
    public function testCreateCartCommandIsValid()
    {
        $cartQuery = $this->createMock(ICartQuery::class);
        $cartQuery->method('findCartById')->willReturn(null);

        $validator = new CreateCartValidator($cartQuery);
        $command = new CreateCartCommand(Uuid::v4());

        $this->expectNotToPerformAssertions();
        $validator->validate($command);
    }

    public function testCreateCartCommandWithExistingCartIdThrowsException()
    {
        $cartId = Uuid::v4();
        $cartQuery = $this->createMock(ICartQuery::class);
        $cartQuery->method('findCartById')->willReturn(new CartDto($cartId->toString()));

        $validator = new CreateCartValidator($cartQuery);
        $command = new CreateCartCommand($cartId->toString());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cart with this ID already exists');
        $validator->validate($command);
    }

    public function testCreateCartCommandWithInvalidCommandThrowsException()
    {
        $cartQuery = $this->createMock(ICartQuery::class);

        $validator = new CreateCartValidator($cartQuery);
        $invalidCommand = $this->createMock(ICommand::class);

        $this->expectException(\InvalidArgumentException::class);
        $validator->validate($invalidCommand);
    }
}