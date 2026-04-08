<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260408062828 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE daily_entries (id INT AUTO_INCREMENT NOT NULL, entry_date DATE NOT NULL, category VARCHAR(20) NOT NULL, co2_value DOUBLE PRECISION NOT NULL, details JSON NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_F2196231A76ED395 (user_id), INDEX IDX_F2196231A76ED395EBC4F69 (user_id, entry_date), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE daily_entries ADD CONSTRAINT FK_F2196231A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE daily_entries DROP FOREIGN KEY FK_F2196231A76ED395');
        $this->addSql('DROP TABLE daily_entries');
        $this->addSql('DROP TABLE users');
    }
}
