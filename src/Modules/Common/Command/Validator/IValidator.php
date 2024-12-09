<?php

namespace App\Modules\Common\Command\Validator;

use App\Modules\Common\Command\ICommand;

interface IValidator
{
    public function validate(ICommand $command): void;
}