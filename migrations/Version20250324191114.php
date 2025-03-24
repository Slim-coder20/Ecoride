<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250324191114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP license_plate, DROP registration_date, DROP model, DROP color, DROP brand, DROP seat, DROP preferences, DROP is_chauffeur, DROP energie, DROP ecologique, DROP note, DROP photo');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD license_plate VARCHAR(255) DEFAULT NULL, ADD registration_date DATETIME DEFAULT NULL, ADD model VARCHAR(255) DEFAULT NULL, ADD color VARCHAR(255) DEFAULT NULL, ADD brand VARCHAR(255) DEFAULT NULL, ADD seat INT DEFAULT NULL, ADD preferences JSON DEFAULT NULL, ADD is_chauffeur TINYINT(1) DEFAULT 0 NOT NULL, ADD energie VARCHAR(50) DEFAULT NULL, ADD ecologique TINYINT(1) NOT NULL, ADD note DOUBLE PRECISION DEFAULT NULL, ADD photo VARCHAR(255) DEFAULT NULL');
    }
}
