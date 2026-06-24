<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260624072543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create the AuditLab schema for MySQL 8.4';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'This migration requires MySQL.',
        );

        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity_log (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(120) NOT NULL, target_type VARCHAR(80) NOT NULL, target_id INT DEFAULT NULL, ip_address VARCHAR(60) DEFAULT NULL, user_agent VARCHAR(255) DEFAULT NULL, details LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, actor_id INT DEFAULT NULL, INDEX IDX_FD06F64710DAF24A (actor_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE app_setting (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, value LONGTEXT NOT NULL, is_sensitive TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE audit_log (id INT AUTO_INCREMENT NOT NULL, level VARCHAR(80) NOT NULL, source VARCHAR(180) NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(160) NOT NULL, email VARCHAR(180) NOT NULL, phone VARCHAR(40) DEFAULT NULL, company VARCHAR(160) NOT NULL, notes LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, owner_id INT NOT NULL, INDEX IDX_C74404557E3C61F9 (owner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, author_id INT NOT NULL, ticket_id INT NOT NULL, INDEX IDX_9474526CF675F31B (author_id), INDEX IDX_9474526C700047D2 (ticket_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, filename VARCHAR(255) NOT NULL, original_filename VARCHAR(255) NOT NULL, mime_type VARCHAR(120) NOT NULL, path VARCHAR(255) NOT NULL, visibility VARCHAR(40) NOT NULL, created_at DATETIME NOT NULL, uploaded_by_id INT NOT NULL, INDEX IDX_D8698A76A2B28FE8 (uploaded_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE internal_note (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, content LONGTEXT NOT NULL, visibility VARCHAR(40) NOT NULL, created_at DATETIME NOT NULL, author_id INT NOT NULL, related_client_id INT DEFAULT NULL, related_project_id INT DEFAULT NULL, INDEX IDX_233D6BDFF675F31B (author_id), INDEX IDX_233D6BDFB12A353A (related_client_id), INDEX IDX_233D6BDF9CA0172C (related_project_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, number VARCHAR(60) NOT NULL, amount INT NOT NULL, status VARCHAR(40) NOT NULL, pdf_path VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, client_id INT NOT NULL, owner_id INT NOT NULL, INDEX IDX_9065174419EB6921 (client_id), INDEX IDX_906517447E3C61F9 (owner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `message` (id INT AUTO_INCREMENT NOT NULL, subject VARCHAR(180) NOT NULL, body LONGTEXT NOT NULL, read_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, sender_id INT NOT NULL, recipient_id INT NOT NULL, INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307FE92F8F78 (recipient_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(160) NOT NULL, description LONGTEXT NOT NULL, budget INT NOT NULL, status VARCHAR(40) NOT NULL, confidential TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, owner_id INT NOT NULL, INDEX IDX_2FB3D0EE7E3C61F9 (owner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE project_member (project_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_67401132166D1F9C (project_id), INDEX IDX_67401132A76ED395 (user_id), PRIMARY KEY (project_id, user_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE setting (id INT AUTO_INCREMENT NOT NULL, key_name VARCHAR(100) NOT NULL, value LONGTEXT NOT NULL, is_sensitive TINYINT NOT NULL, updated_at DATETIME NOT NULL, updated_by_id INT DEFAULT NULL, INDEX IDX_9F74B898896DBBDE (updated_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `task` (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, description LONGTEXT NOT NULL, status VARCHAR(40) NOT NULL, priority VARCHAR(40) NOT NULL, due_date DATETIME DEFAULT NULL, assigned_to_id INT DEFAULT NULL, project_id INT NOT NULL, created_by_id INT NOT NULL, INDEX IDX_527EDB25F4BD7827 (assigned_to_id), INDEX IDX_527EDB25166D1F9C (project_id), INDEX IDX_527EDB25B03A8386 (created_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, description LONGTEXT NOT NULL, status VARCHAR(40) NOT NULL, priority VARCHAR(40) NOT NULL, created_at DATETIME NOT NULL, customer_id INT NOT NULL, created_by_id INT NOT NULL, assigned_to_id INT DEFAULT NULL, INDEX IDX_97A0ADA39395C3F3 (customer_id), INDEX IDX_97A0ADA3B03A8386 (created_by_id), INDEX IDX_97A0ADA3F4BD7827 (assigned_to_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, full_name VARCHAR(120) NOT NULL, department VARCHAR(80) DEFAULT NULL, bio LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE webhook_endpoint (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, url VARCHAR(255) NOT NULL, event_type VARCHAR(80) NOT NULL, secret VARCHAR(160) NOT NULL, active TINYINT NOT NULL, created_by_id INT NOT NULL, INDEX IDX_3AB88953B03A8386 (created_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE activity_log ADD CONSTRAINT FK_FD06F64710DAF24A FOREIGN KEY (actor_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C74404557E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76A2B28FE8 FOREIGN KEY (uploaded_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE internal_note ADD CONSTRAINT FK_233D6BDFF675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE internal_note ADD CONSTRAINT FK_233D6BDFB12A353A FOREIGN KEY (related_client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE internal_note ADD CONSTRAINT FK_233D6BDF9CA0172C FOREIGN KEY (related_project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174419EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517447E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `message` ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `message` ADD CONSTRAINT FK_B6BD307FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE7E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE project_member ADD CONSTRAINT FK_67401132166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_member ADD CONSTRAINT FK_67401132A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE setting ADD CONSTRAINT FK_9F74B898896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `task` ADD CONSTRAINT FK_527EDB25F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `task` ADD CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE `task` ADD CONSTRAINT FK_527EDB25B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA39395C3F3 FOREIGN KEY (customer_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE webhook_endpoint ADD CONSTRAINT FK_3AB88953B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'This migration requires MySQL.',
        );

        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_log DROP FOREIGN KEY FK_FD06F64710DAF24A');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C74404557E3C61F9');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C700047D2');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76A2B28FE8');
        $this->addSql('ALTER TABLE internal_note DROP FOREIGN KEY FK_233D6BDFF675F31B');
        $this->addSql('ALTER TABLE internal_note DROP FOREIGN KEY FK_233D6BDFB12A353A');
        $this->addSql('ALTER TABLE internal_note DROP FOREIGN KEY FK_233D6BDF9CA0172C');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_9065174419EB6921');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_906517447E3C61F9');
        $this->addSql('ALTER TABLE `message` DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE `message` DROP FOREIGN KEY FK_B6BD307FE92F8F78');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE7E3C61F9');
        $this->addSql('ALTER TABLE project_member DROP FOREIGN KEY FK_67401132166D1F9C');
        $this->addSql('ALTER TABLE project_member DROP FOREIGN KEY FK_67401132A76ED395');
        $this->addSql('ALTER TABLE setting DROP FOREIGN KEY FK_9F74B898896DBBDE');
        $this->addSql('ALTER TABLE `task` DROP FOREIGN KEY FK_527EDB25F4BD7827');
        $this->addSql('ALTER TABLE `task` DROP FOREIGN KEY FK_527EDB25166D1F9C');
        $this->addSql('ALTER TABLE `task` DROP FOREIGN KEY FK_527EDB25B03A8386');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA39395C3F3');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3B03A8386');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3F4BD7827');
        $this->addSql('ALTER TABLE webhook_endpoint DROP FOREIGN KEY FK_3AB88953B03A8386');
        $this->addSql('DROP TABLE activity_log');
        $this->addSql('DROP TABLE app_setting');
        $this->addSql('DROP TABLE audit_log');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE internal_note');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE `message`');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_member');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP TABLE `task`');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE webhook_endpoint');
    }
}
