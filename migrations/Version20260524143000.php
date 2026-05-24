<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260524143000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create EventSecure Lab domain tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, full_name VARCHAR(100) NOT NULL, company VARCHAR(120) DEFAULT NULL, avatar_path VARCHAR(255) DEFAULT NULL, phone VARCHAR(80) DEFAULT NULL, internal_note VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('CREATE TABLE event (id SERIAL NOT NULL, title VARCHAR(160) NOT NULL, description TEXT NOT NULL, location VARCHAR(120) NOT NULL, starts_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, capacity INT NOT NULL, price_cents INT NOT NULL, category VARCHAR(40) NOT NULL, published BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE registration (id SERIAL NOT NULL, user_id INT NOT NULL, event_id INT NOT NULL, registered_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(30) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_62A8A7A7A76ED395 ON registration (user_id)');
        $this->addSql('CREATE INDEX IDX_62A8A7A771F7E88B ON registration (event_id)');
        $this->addSql('CREATE TABLE comment (id SERIAL NOT NULL, event_id INT NOT NULL, author_id INT NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9474526C71F7E88B ON comment (event_id)');
        $this->addSql('CREATE INDEX IDX_9474526CF675F31B ON comment (author_id)');
        $this->addSql('CREATE TABLE invoice (id SERIAL NOT NULL, user_id INT NOT NULL, number VARCHAR(40) NOT NULL, amount_cents INT NOT NULL, file_path VARCHAR(255) NOT NULL, issued_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_90651744A76ED395 ON invoice (user_id)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A7A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A771F7E88B FOREIGN KEY (event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE registration');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE "user"');
    }
}
