<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250205220442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('DROP TABLE category');
        $this->addSql('ALTER TABLE comment ADD author_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD post_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD content TEXT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE comment DROP comment_content');
        $this->addSql('COMMENT ON COLUMN comment.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9474526CF675F31B ON comment (author_id)');
        $this->addSql('CREATE INDEX IDX_9474526C4B89032C ON comment (post_id)');
        $this->addSql('ALTER TABLE post ADD author_id INT NOT NULL');
        $this->addSql('ALTER TABLE post ADD type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DF675F31B ON post (author_id)');
        $this->addSql('ALTER TABLE "user" DROP updated_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE category (id SERIAL NOT NULL, "тфname" VARCHAR(25) NOT NULL, type VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C4B89032C');
        $this->addSql('DROP INDEX IDX_9474526CF675F31B');
        $this->addSql('DROP INDEX IDX_9474526C4B89032C');
        $this->addSql('ALTER TABLE comment ADD comment_content VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE comment DROP author_id');
        $this->addSql('ALTER TABLE comment DROP post_id');
        $this->addSql('ALTER TABLE comment DROP content');
        $this->addSql('ALTER TABLE comment DROP created_at');
        $this->addSql('ALTER TABLE "user" ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN "user".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8DF675F31B');
        $this->addSql('DROP INDEX IDX_5A8A6C8DF675F31B');
        $this->addSql('ALTER TABLE post DROP author_id');
        $this->addSql('ALTER TABLE post DROP type');
    }
}
