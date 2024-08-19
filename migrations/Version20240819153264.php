<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240819153264 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Check if the 'api_token' column already exists
        if (!$schema->getTable('user')->hasColumn('api_token')) {
            $this->addSql('ALTER TABLE user ADD api_token VARCHAR(255) DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        // Optionally remove api_token column if rolling back
        $this->addSql('ALTER TABLE user DROP api_token');
    }
}
