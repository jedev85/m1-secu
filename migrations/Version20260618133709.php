<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260618133709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity_log (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "action" VARCHAR(120) NOT NULL, target_type VARCHAR(80) NOT NULL, target_id INTEGER DEFAULT NULL, ip_address VARCHAR(60) DEFAULT NULL, user_agent VARCHAR(255) DEFAULT NULL, details CLOB DEFAULT NULL, created_at DATETIME NOT NULL, actor_id INTEGER DEFAULT NULL, CONSTRAINT FK_FD06F64710DAF24A FOREIGN KEY (actor_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_FD06F64710DAF24A ON activity_log (actor_id)');
        $this->addSql('CREATE TABLE internal_note (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(180) NOT NULL, content CLOB NOT NULL, visibility VARCHAR(40) NOT NULL, created_at DATETIME NOT NULL, author_id INTEGER NOT NULL, related_client_id INTEGER DEFAULT NULL, related_project_id INTEGER DEFAULT NULL, CONSTRAINT FK_233D6BDFF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_233D6BDFB12A353A FOREIGN KEY (related_client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_233D6BDF9CA0172C FOREIGN KEY (related_project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_233D6BDFF675F31B ON internal_note (author_id)');
        $this->addSql('CREATE INDEX IDX_233D6BDFB12A353A ON internal_note (related_client_id)');
        $this->addSql('CREATE INDEX IDX_233D6BDF9CA0172C ON internal_note (related_project_id)');
        $this->addSql('CREATE TABLE "message" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, subject VARCHAR(180) NOT NULL, body CLOB NOT NULL, read_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, sender_id INTEGER NOT NULL, recipient_id INTEGER NOT NULL, CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B6BD307FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_B6BD307FF624B39D ON "message" (sender_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FE92F8F78 ON "message" (recipient_id)');
        $this->addSql('CREATE TABLE project (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(160) NOT NULL, description CLOB NOT NULL, budget INTEGER NOT NULL, status VARCHAR(40) NOT NULL, confidential BOOLEAN NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, owner_id INTEGER NOT NULL, CONSTRAINT FK_2FB3D0EE7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE7E3C61F9 ON project (owner_id)');
        $this->addSql('CREATE TABLE project_member (project_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY (project_id, user_id), CONSTRAINT FK_67401132166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_67401132A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_67401132166D1F9C ON project_member (project_id)');
        $this->addSql('CREATE INDEX IDX_67401132A76ED395 ON project_member (user_id)');
        $this->addSql('CREATE TABLE setting (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, key_name VARCHAR(100) NOT NULL, value CLOB NOT NULL, is_sensitive BOOLEAN NOT NULL, updated_at DATETIME NOT NULL, updated_by_id INTEGER DEFAULT NULL, CONSTRAINT FK_9F74B898896DBBDE FOREIGN KEY (updated_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_9F74B898896DBBDE ON setting (updated_by_id)');
        $this->addSql('CREATE TABLE "task" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(180) NOT NULL, description CLOB NOT NULL, status VARCHAR(40) NOT NULL, priority VARCHAR(40) NOT NULL, due_date DATETIME DEFAULT NULL, assigned_to_id INTEGER DEFAULT NULL, project_id INTEGER NOT NULL, created_by_id INTEGER NOT NULL, CONSTRAINT FK_527EDB25F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_527EDB25B03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_527EDB25F4BD7827 ON "task" (assigned_to_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25166D1F9C ON "task" (project_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25B03A8386 ON "task" (created_by_id)');
        $this->addSql('CREATE TABLE webhook_endpoint (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(120) NOT NULL, url VARCHAR(255) NOT NULL, event_type VARCHAR(80) NOT NULL, secret VARCHAR(160) NOT NULL, active BOOLEAN NOT NULL, created_by_id INTEGER NOT NULL, CONSTRAINT FK_3AB88953B03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_3AB88953B03A8386 ON webhook_endpoint (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE activity_log');
        $this->addSql('DROP TABLE internal_note');
        $this->addSql('DROP TABLE "message"');
        $this->addSql('DROP TABLE project_member');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP TABLE "task"');
        $this->addSql('DROP TABLE webhook_endpoint');
    }
}
