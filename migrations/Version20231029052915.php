<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231029052915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `useroptions` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, value JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', autoload TINYINT(1) DEFAULT 1 NOT NULL, description VARCHAR(255) NOT NULL, type VARCHAR(20) NOT NULL, INDEX IDX_4C540639A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `useroptions` ADD CONSTRAINT FK_4C540639A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `useroptions` DROP FOREIGN KEY FK_4C540639A76ED395');
        $this->addSql('DROP TABLE `useroptions`');
    }
}
