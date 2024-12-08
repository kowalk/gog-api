<?php

namespace App\Modules\Product\Command\Validator;

use App\Modules\Common\Command\ICommand;
use App\Shared\Repository\ProductRepository;

interface IValidator
{
    public function validate(ICommand $command): void;
}