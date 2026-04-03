<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260401102217 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, category VARCHAR(50) NOT NULL, type VARCHAR(100) DEFAULT NULL, distance DOUBLE PRECISION DEFAULT NULL, quantity INT DEFAULT NULL, co2_emitted DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, locale VARCHAR(5) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE emission_factor (id INT AUTO_INCREMENT NOT NULL, category VARCHAR(50) NOT NULL, slug VARCHAR(100) NOT NULL, label_fr VARCHAR(100) NOT NULL, label_en VARCHAR(100) NOT NULL, icon VARCHAR(10) NOT NULL, factor_per_unit DOUBLE PRECISION NOT NULL, unit VARCHAR(20) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, points INT DEFAULT 0 NOT NULL, level VARCHAR(255) DEFAULT NULL, badges JSON DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE emission_factor');
        $this->addSql('DROP TABLE user');
    }
}
