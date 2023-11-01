<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231101211536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        if ($this->connection->getDatabasePlatform() instanceof SqlitePlatform)
        {
            // this up() migration is auto-generated, please modify it to your needs
            $this->addSql('CREATE TABLE access_token (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , permanent BOOLEAN NOT NULL, name VARCHAR(180) DEFAULT NULL, CONSTRAINT FK_B6A2DD68A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_B6A2DD685F37A13B ON access_token (token)');
            $this->addSql('CREATE INDEX IDX_B6A2DD68A76ED395 ON access_token (user_id)');
            $this->addSql('CREATE TABLE "options" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, value CLOB DEFAULT NULL --(DC2Type:json)
        , autoload BOOLEAN DEFAULT 1 NOT NULL, description VARCHAR(255) NOT NULL, type VARCHAR(20) NOT NULL)');
            $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, fullname VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, enabled BOOLEAN DEFAULT 1 NOT NULL)');
            $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
            $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
            $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
            $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        }
    }

    public function down(Schema $schema): void
    {
        if ($this->connection->getDatabasePlatform() instanceof SqlitePlatform)
        {
            // this down() migration is auto-generated, please modify it to your needs
            $this->addSql('DROP TABLE access_token');
            $this->addSql('DROP TABLE "options"');
            $this->addSql('DROP TABLE user');
            $this->addSql('DROP TABLE messenger_messages');
        }
    }
}
