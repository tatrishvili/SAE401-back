<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260407000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add XP and badge gamification support for users.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` ADD xp INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE badge CHANGE image_name image_url VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE badge ADD xp_threshold INT NOT NULL DEFAULT 0');
        $this->addSql('CREATE TABLE user_badge (user_id INT NOT NULL, badge_id INT NOT NULL, INDEX IDX_1C32B345A76ED395 (user_id), INDEX IDX_1C32B345F7A2C2FC (badge_id), PRIMARY KEY(user_id, badge_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_badge ADD CONSTRAINT FK_USER_BADGE_USER_ID FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_badge ADD CONSTRAINT FK_USER_BADGE_BADGE_ID FOREIGN KEY (badge_id) REFERENCES badge (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_badge DROP FOREIGN KEY FK_USER_BADGE_USER_ID');
        $this->addSql('ALTER TABLE user_badge DROP FOREIGN KEY FK_USER_BADGE_BADGE_ID');
        $this->addSql('DROP TABLE user_badge');
        $this->addSql('ALTER TABLE badge DROP xp_threshold');
        $this->addSql('ALTER TABLE badge CHANGE image_url image_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE `user` DROP xp');
    }
}