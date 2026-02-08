<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260208171614 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie_cours (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, date_de_creation DATETIME DEFAULT NULL, date_de_modification DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8B2614C6C6E55B5 (nom), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE collab_request (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, budget NUMERIC(10, 2) DEFAULT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, status VARCHAR(50) NOT NULL, rejection_reason LONGTEXT DEFAULT NULL, deliverables LONGTEXT DEFAULT NULL, payment_terms LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, responded_at DATETIME DEFAULT NULL, creator_id INT DEFAULT NULL, revisor_id INT DEFAULT NULL, collaborator_id INT NOT NULL, INDEX IDX_195F8ECF61220EA6 (creator_id), INDEX IDX_195F8ECFBD3183DF (revisor_id), INDEX IDX_195F8ECF30098C8C (collaborator_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE collaborator (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, company_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(50) DEFAULT NULL, address LONGTEXT DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, domain VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, is_public TINYINT NOT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, added_by_user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_606D487CE7927C74 (email), INDEX IDX_606D487CCA792C6B (added_by_user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE contract (id INT AUTO_INCREMENT NOT NULL, contract_number VARCHAR(100) NOT NULL, title VARCHAR(255) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, amount NUMERIC(10, 2) NOT NULL, pdf_path VARCHAR(255) DEFAULT NULL, status VARCHAR(50) NOT NULL, signed_by_creator TINYINT NOT NULL, signed_by_collaborator TINYINT NOT NULL, creator_signature_date DATETIME DEFAULT NULL, collaborator_signature_date DATETIME DEFAULT NULL, terms LONGTEXT DEFAULT NULL, payment_schedule LONGTEXT DEFAULT NULL, confidentiality_clause LONGTEXT DEFAULT NULL, cancellation_terms LONGTEXT DEFAULT NULL, signature_token VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, sent_at DATETIME DEFAULT NULL, collab_request_id INT NOT NULL, creator_id INT DEFAULT NULL, collaborator_id INT NOT NULL, UNIQUE INDEX UNIQ_E98F2859AAD0FA19 (contract_number), UNIQUE INDEX UNIQ_E98F2859A266EDF4 (collab_request_id), INDEX IDX_E98F285961220EA6 (creator_id), INDEX IDX_E98F285930098C8C (collaborator_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, image VARCHAR(255) DEFAULT NULL, date_de_creation DATETIME DEFAULT NULL, date_de_modification DATETIME DEFAULT NULL, categorie_id INT NOT NULL, INDEX IDX_FDCA8C9CBCF5E72D (categorie_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ressource (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, contenu LONGTEXT DEFAULT NULL, date_de_creation DATETIME DEFAULT NULL, date_de_modification DATETIME DEFAULT NULL, cours_id INT NOT NULL, INDEX IDX_939F45447ECF78B0 (cours_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE collab_request ADD CONSTRAINT FK_195F8ECF61220EA6 FOREIGN KEY (creator_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE collab_request ADD CONSTRAINT FK_195F8ECFBD3183DF FOREIGN KEY (revisor_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE collab_request ADD CONSTRAINT FK_195F8ECF30098C8C FOREIGN KEY (collaborator_id) REFERENCES collaborator (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE collaborator ADD CONSTRAINT FK_606D487CCA792C6B FOREIGN KEY (added_by_user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F2859A266EDF4 FOREIGN KEY (collab_request_id) REFERENCES collab_request (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F285961220EA6 FOREIGN KEY (creator_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F285930098C8C FOREIGN KEY (collaborator_id) REFERENCES collaborator (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie_cours (id)');
        $this->addSql('ALTER TABLE ressource ADD CONSTRAINT FK_939F45447ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE post DROP group_id');
        $this->addSql('ALTER TABLE users ADD manager_id INT DEFAULT NULL, ADD creator_id INT DEFAULT NULL');
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
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CBCF5E72D');
        $this->addSql('ALTER TABLE ressource DROP FOREIGN KEY FK_939F45447ECF78B0');
        $this->addSql('DROP TABLE categorie_cours');
        $this->addSql('DROP TABLE collab_request');
        $this->addSql('DROP TABLE collaborator');
        $this->addSql('DROP TABLE contract');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE ressource');
        $this->addSql('ALTER TABLE post ADD group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users DROP manager_id, DROP creator_id');
    }
}
