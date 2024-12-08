<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241207194100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add USD currency to the currency table';
    }

    public function up(Schema $schema): void
    {
        // This migration adds the USD currency to the currency table
        $this->addSql("INSERT INTO currency (code, name) VALUES ('USD', 'United States Dollar')");
    }

    public function down(Schema $schema): void
    {
        // This migration removes the USD currency from the currency table
        $this->addSql("DELETE FROM currency WHERE code = 'USD'");
    }
}
