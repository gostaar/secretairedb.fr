<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250128162933 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE identifiants_user DROP CONSTRAINT fk_576a0c93d462b653');
        $this->addSql('ALTER TABLE identifiants_user DROP CONSTRAINT fk_576a0c93a76ed395');
        $this->addSql('DROP TABLE identifiants_user');
        $this->addSql('ALTER TABLE identifiants ADD users_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE identifiants ADD CONSTRAINT FK_B27B655167B3B43D FOREIGN KEY (users_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B27B655167B3B43D ON identifiants (users_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE identifiants_user (identifiants_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(identifiants_id, user_id))');
        $this->addSql('CREATE INDEX idx_576a0c93a76ed395 ON identifiants_user (user_id)');
        $this->addSql('CREATE INDEX idx_576a0c93d462b653 ON identifiants_user (identifiants_id)');
        $this->addSql('ALTER TABLE identifiants_user ADD CONSTRAINT fk_576a0c93d462b653 FOREIGN KEY (identifiants_id) REFERENCES identifiants (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE identifiants_user ADD CONSTRAINT fk_576a0c93a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE identifiants DROP CONSTRAINT FK_B27B655167B3B43D');
        $this->addSql('DROP INDEX IDX_B27B655167B3B43D');
        $this->addSql('ALTER TABLE identifiants DROP users_id');
    }
}
