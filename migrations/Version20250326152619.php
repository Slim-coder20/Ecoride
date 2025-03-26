<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250326152619 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chauffeur DROP license_plate, DROP registration_date, DROP model, DROP brand, DROP color, DROP seat, DROP energie, DROP ecologique, DROP photo');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chauffeur ADD license_plate VARCHAR(255) NOT NULL, ADD registration_date DATETIME NOT NULL, ADD model VARCHAR(255) NOT NULL, ADD brand VARCHAR(255) NOT NULL, ADD color VARCHAR(255) NOT NULL, ADD seat INT NOT NULL, ADD energie VARCHAR(255) NOT NULL, ADD ecologique TINYINT(1) NOT NULL, ADD photo VARCHAR(255) DEFAULT NULL');
    }
}
