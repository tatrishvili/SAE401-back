<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260425161837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE badge (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image_url VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, xp_threshold INT DEFAULT 0 NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE challenge (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, category VARCHAR(255) NOT NULL, co2_reward DOUBLE PRECISION NOT NULL, is_daily TINYINT NOT NULL, step_id INT NOT NULL, INDEX IDX_D709895173B21E9C (step_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE daily_entries (id INT AUTO_INCREMENT NOT NULL, entry_date DATE NOT NULL, category VARCHAR(20) NOT NULL, co2_value DOUBLE PRECISION NOT NULL, details JSON NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_F2196231A76ED395 (user_id), INDEX IDX_F2196231A76ED395EBC4F69 (user_id, entry_date), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE step (id INT AUTO_INCREMENT NOT NULL, position INT NOT NULL, title VARCHAR(255) NOT NULL, is_unlocked TINYINT NOT NULL, is_completed TINYINT NOT NULL, validated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(100) DEFAULT NULL, xp INT DEFAULT 0 NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_badge (user_id INT NOT NULL, badge_id INT NOT NULL, INDEX IDX_1C32B345A76ED395 (user_id), INDEX IDX_1C32B345F7A2C2FC (badge_id), PRIMARY KEY (user_id, badge_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_challenge (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(255) NOT NULL, completed_at DATETIME DEFAULT NULL, owner_id INT NOT NULL, challenge_id INT NOT NULL, INDEX IDX_D7E904B57E3C61F9 (owner_id), INDEX IDX_D7E904B598A21AC6 (challenge_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE challenge ADD CONSTRAINT FK_D709895173B21E9C FOREIGN KEY (step_id) REFERENCES step (id)');
        $this->addSql('ALTER TABLE daily_entries ADD CONSTRAINT FK_F2196231A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_badge ADD CONSTRAINT FK_1C32B345A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_badge ADD CONSTRAINT FK_1C32B345F7A2C2FC FOREIGN KEY (badge_id) REFERENCES badge (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_challenge ADD CONSTRAINT FK_D7E904B57E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_challenge ADD CONSTRAINT FK_D7E904B598A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge DROP FOREIGN KEY FK_D709895173B21E9C');
        $this->addSql('ALTER TABLE daily_entries DROP FOREIGN KEY FK_F2196231A76ED395');
        $this->addSql('ALTER TABLE user_badge DROP FOREIGN KEY FK_1C32B345A76ED395');
        $this->addSql('ALTER TABLE user_badge DROP FOREIGN KEY FK_1C32B345F7A2C2FC');
        $this->addSql('ALTER TABLE user_challenge DROP FOREIGN KEY FK_D7E904B57E3C61F9');
        $this->addSql('ALTER TABLE user_challenge DROP FOREIGN KEY FK_D7E904B598A21AC6');
        $this->addSql('DROP TABLE badge');
        $this->addSql('DROP TABLE challenge');
        $this->addSql('DROP TABLE daily_entries');
        $this->addSql('DROP TABLE step');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_badge');
        $this->addSql('DROP TABLE user_challenge');
    }
}
