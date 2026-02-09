<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260208174722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE collab_request (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, budget NUMERIC(10, 2) DEFAULT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, status VARCHAR(50) NOT NULL, rejection_reason LONGTEXT DEFAULT NULL, deliverables LONGTEXT DEFAULT NULL, payment_terms LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, responded_at DATETIME DEFAULT NULL, creator_id INT DEFAULT NULL, revisor_id INT DEFAULT NULL, collaborator_id INT NOT NULL, INDEX IDX_195F8ECF61220EA6 (creator_id), INDEX IDX_195F8ECFBD3183DF (revisor_id), INDEX IDX_195F8ECF30098C8C (collaborator_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE collaborator (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, company_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(50) DEFAULT NULL, address LONGTEXT DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, domain VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, is_public TINYINT NOT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, added_by_user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_606D487CE7927C74 (email), INDEX IDX_606D487CCA792C6B (added_by_user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE contract (id INT AUTO_INCREMENT NOT NULL, contract_number VARCHAR(100) NOT NULL, title VARCHAR(255) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, amount NUMERIC(10, 2) NOT NULL, pdf_path VARCHAR(255) DEFAULT NULL, status VARCHAR(50) NOT NULL, signed_by_creator TINYINT NOT NULL, signed_by_collaborator TINYINT NOT NULL, creator_signature_date DATETIME DEFAULT NULL, collaborator_signature_date DATETIME DEFAULT NULL, terms LONGTEXT DEFAULT NULL, payment_schedule LONGTEXT DEFAULT NULL, confidentiality_clause LONGTEXT DEFAULT NULL, cancellation_terms LONGTEXT DEFAULT NULL, signature_token VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, sent_at DATETIME DEFAULT NULL, collab_request_id INT NOT NULL, creator_id INT DEFAULT NULL, collaborator_id INT NOT NULL, UNIQUE INDEX UNIQ_E98F2859AAD0FA19 (contract_number), UNIQUE INDEX UNIQ_E98F2859A266EDF4 (collab_request_id), INDEX IDX_E98F285961220EA6 (creator_id), INDEX IDX_E98F285930098C8C (collaborator_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE collab_request ADD CONSTRAINT FK_195F8ECF61220EA6 FOREIGN KEY (creator_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE collab_request ADD CONSTRAINT FK_195F8ECFBD3183DF FOREIGN KEY (revisor_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE collab_request ADD CONSTRAINT FK_195F8ECF30098C8C FOREIGN KEY (collaborator_id) REFERENCES collaborator (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE collaborator ADD CONSTRAINT FK_606D487CCA792C6B FOREIGN KEY (added_by_user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F2859A266EDF4 FOREIGN KEY (collab_request_id) REFERENCES collab_request (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F285961220EA6 FOREIGN KEY (creator_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F285930098C8C FOREIGN KEY (collaborator_id) REFERENCES collaborator (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8B2614C6C6E55B5 ON categorie_cours (nom)');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY `FK_9474526C186CE3E1`');
        $this->addSql('DROP INDEX IDX_9474526C186CE3E1 ON comment');
        $this->addSql('ALTER TABLE comment CHANGE body body LONGTEXT NOT NULL, CHANGE status status VARCHAR(50) NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE replay_id parent_comment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CBF2AF943 FOREIGN KEY (parent_comment_id) REFERENCES comment (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_9474526CBF2AF943 ON comment (parent_comment_id)');
        $this->addSql('ALTER TABLE post ADD content LONGTEXT NOT NULL, ADD tags VARCHAR(255) DEFAULT NULL, ADD image_name VARCHAR(255) DEFAULT NULL, ADD pdf_name VARCHAR(255) DEFAULT NULL, ADD likes INT DEFAULT 0 NOT NULL, ADD solution_id INT DEFAULT NULL, CHANGE status status VARCHAR(50) NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE pinned pinned TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D1C0BE183 FOREIGN KEY (solution_id) REFERENCES comment (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A8A6C8D1C0BE183 ON post (solution_id)');
        $this->addSql('ALTER TABLE users ADD manager_id INT DEFAULT NULL, ADD creator_id INT DEFAULT NULL, CHANGE groupid groupid INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE collab_request DROP FOREIGN KEY FK_195F8ECF61220EA6');
        $this->addSql('ALTER TABLE collab_request DROP FOREIGN KEY FK_195F8ECFBD3183DF');
        $this->addSql('ALTER TABLE collab_request DROP FOREIGN KEY FK_195F8ECF30098C8C');
        $this->addSql('ALTER TABLE collaborator DROP FOREIGN KEY FK_606D487CCA792C6B');
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F2859A266EDF4');
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F285961220EA6');
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F285930098C8C');
        $this->addSql('DROP TABLE collab_request');
        $this->addSql('DROP TABLE collaborator');
        $this->addSql('DROP TABLE contract');
        $this->addSql('DROP INDEX UNIQ_8B2614C6C6E55B5 ON categorie_cours');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CBF2AF943');
        $this->addSql('DROP INDEX IDX_9474526CBF2AF943 ON comment');
        $this->addSql('ALTER TABLE comment CHANGE body body VARCHAR(255) NOT NULL, CHANGE status status VARCHAR(255) NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL, CHANGE parent_comment_id replay_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT `FK_9474526C186CE3E1` FOREIGN KEY (replay_id) REFERENCES comment (id)');
        $this->addSql('CREATE INDEX IDX_9474526C186CE3E1 ON comment (replay_id)');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D1C0BE183');
        $this->addSql('DROP INDEX UNIQ_5A8A6C8D1C0BE183 ON post');
        $this->addSql('ALTER TABLE post DROP content, DROP tags, DROP image_name, DROP pdf_name, DROP likes, DROP solution_id, CHANGE status status VARCHAR(255) NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL, CHANGE pinned pinned TINYINT NOT NULL');
        $this->addSql('ALTER TABLE users DROP manager_id, DROP creator_id, CHANGE groupid groupid INT NOT NULL');
    }
}
