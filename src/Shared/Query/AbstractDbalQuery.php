<?php

namespace App\Shared\Query;

use Doctrine\DBAL\Connection;

abstract class AbstractDbalQuery
{
    protected Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
}
