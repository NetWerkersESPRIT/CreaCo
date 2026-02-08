<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260207213951 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, type VARCHAR(20) NOT NULL, category VARCHAR(30) NOT NULL, date DATE NOT NULL, time TIME NOT NULL, organizer VARCHAR(255) NOT NULL, is_for_all_users TINYINT NOT NULL, meeting_link VARCHAR(255) DEFAULT NULL, platform VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, google_maps_link VARCHAR(255) DEFAULT NULL, capacity INT DEFAULT NULL, contact VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE event_users (event_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_559814C571F7E88B (event_id), INDEX IDX_559814C567B3B43D (users_id), PRIMARY KEY (event_id, users_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, reserved_at DATETIME NOT NULL, status VARCHAR(255) NOT NULL, event_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_42C8495571F7E88B (event_id), INDEX IDX_42C84955A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, groupid INT NOT NULL, numtel VARCHAR(20) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE event_users ADD CONSTRAINT FK_559814C571F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_users ADD CONSTRAINT FK_559814C567B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495571F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_users DROP FOREIGN KEY FK_559814C571F7E88B');
        $this->addSql('ALTER TABLE event_users DROP FOREIGN KEY FK_559814C567B3B43D');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495571F7E88B');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_users');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
