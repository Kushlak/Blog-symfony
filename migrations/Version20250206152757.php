<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250206152757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE post_key_value_store (id SERIAL NOT NULL, post_id INT NOT NULL, key VARCHAR(255) NOT NULL, value JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_16A91E774B89032C ON post_key_value_store (post_id)');
        $this->addSql('ALTER TABLE post_key_value_store ADD CONSTRAINT FK_16A91E774B89032C FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE post_key_value_store DROP CONSTRAINT FK_16A91E774B89032C');
        $this->addSql('DROP TABLE post_key_value_store');
    }
}
