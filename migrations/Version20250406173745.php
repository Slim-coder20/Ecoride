<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250406173745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE employe DROP FOREIGN KEY FK_F804D3B9A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE employe ADD CONSTRAINT FK_F804D3B9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE employe DROP FOREIGN KEY FK_F804D3B9A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE employe ADD CONSTRAINT FK_F804D3B9A76ED395 FOREIGN KEY (user_id) REFERENCES employe (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
    }
}
