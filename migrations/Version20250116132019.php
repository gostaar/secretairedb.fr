<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250116132019 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact (id SERIAL NOT NULL, repertoire_id INT DEFAULT NULL, user_id INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, telephone VARCHAR(20) DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, role VARCHAR(100) DEFAULT NULL, commentaire VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4C62E6381E61B789 ON contact (repertoire_id)');
        $this->addSql('CREATE INDEX IDX_4C62E638A76ED395 ON contact (user_id)');
        $this->addSql('CREATE TABLE devis (id SERIAL NOT NULL, client_id INT DEFAULT NULL, montant NUMERIC(10, 2) DEFAULT NULL, date_devis TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, commentaire VARCHAR(255) DEFAULT NULL, is_active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8B27C52B19EB6921 ON devis (client_id)');
        $this->addSql('COMMENT ON COLUMN devis.date_devis IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE devis_ligne (id SERIAL NOT NULL, devis_id INT NOT NULL, designation VARCHAR(255) NOT NULL, quantite INT DEFAULT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_41D3C6A741DEFADA ON devis_ligne (devis_id)');
        $this->addSql('CREATE TABLE devis_version (id SERIAL NOT NULL, montant NUMERIC(10, 2) DEFAULT NULL, commentaire VARCHAR(255) DEFAULT NULL, is_active BOOLEAN NOT NULL, status VARCHAR(255) NOT NULL, date_modification TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, version VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE documents_utilisateur (id SERIAL NOT NULL, user_id INT DEFAULT NULL, dossier_id INT DEFAULT NULL, type_document_id INT DEFAULT NULL, date_document DATE NOT NULL, name VARCHAR(255) DEFAULT NULL, expediteur VARCHAR(255) DEFAULT NULL, destinataire VARCHAR(255) DEFAULT NULL, is_active BOOLEAN NOT NULL, details VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C7800232A76ED395 ON documents_utilisateur (user_id)');
        $this->addSql('CREATE INDEX IDX_C7800232611C0C56 ON documents_utilisateur (dossier_id)');
        $this->addSql('CREATE INDEX IDX_C78002328826AFA6 ON documents_utilisateur (type_document_id)');
        $this->addSql('CREATE TABLE dossier (id SERIAL NOT NULL, user_id INT DEFAULT NULL, services_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3D48E037A76ED395 ON dossier (user_id)');
        $this->addSql('CREATE INDEX IDX_3D48E037AEF5A6C1 ON dossier (services_id)');
        $this->addSql('CREATE TABLE events (id SERIAL NOT NULL, user_id INT DEFAULT NULL, services_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, location VARCHAR(255) DEFAULT NULL, start TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, "end" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, google_calendar_event_id TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5387574AA76ED395 ON events (user_id)');
        $this->addSql('CREATE INDEX IDX_5387574AAEF5A6C1 ON events (services_id)');
        $this->addSql('COMMENT ON COLUMN events.google_calendar_event_id IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE facture (id SERIAL NOT NULL, client_id INT DEFAULT NULL, montant NUMERIC(10, 2) NOT NULL, date_paiement TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_facture TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, status VARCHAR(255) NOT NULL, commentaire TEXT NOT NULL, is_active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FE86641019EB6921 ON facture (client_id)');
        $this->addSql('CREATE TABLE facture_ligne (id SERIAL NOT NULL, facture_id INT NOT NULL, designation VARCHAR(255) NOT NULL, quantite INT NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C5C453347F2DEE08 ON facture_ligne (facture_id)');
        $this->addSql('CREATE TABLE image (id SERIAL NOT NULL, document_id INT DEFAULT NULL, image_name VARCHAR(255) NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, image_description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C53D045FC33F7837 ON image (document_id)');
        $this->addSql('COMMENT ON COLUMN image.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN image.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE paiement (id SERIAL NOT NULL, facture_id INT DEFAULT NULL, montant_paye NUMERIC(10, 2) NOT NULL, date_paiement TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B1DC7A1E7F2DEE08 ON paiement (facture_id)');
        $this->addSql('CREATE TABLE repertoire (id SERIAL NOT NULL, user_id INT DEFAULT NULL, dossier_id INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, code_postal VARCHAR(10) DEFAULT NULL, ville VARCHAR(255) DEFAULT NULL, pays VARCHAR(255) DEFAULT NULL, telephone VARCHAR(20) DEFAULT NULL, mobile VARCHAR(20) DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, siret VARCHAR(20) DEFAULT NULL, nom_entreprise VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3C367876A76ED395 ON repertoire (user_id)');
        $this->addSql('CREATE INDEX IDX_3C367876611C0C56 ON repertoire (dossier_id)');
        $this->addSql('CREATE TABLE services (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE services_user (services_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(services_id, user_id))');
        $this->addSql('CREATE INDEX IDX_9AA8EF8AEF5A6C1 ON services_user (services_id)');
        $this->addSql('CREATE INDEX IDX_9AA8EF8A76ED395 ON services_user (user_id)');
        $this->addSql('CREATE TABLE type_document (id SERIAL NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1596AD8AA76ED395 ON type_document (user_id)');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, last_activity TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) DEFAULT NULL, code_postal VARCHAR(255) DEFAULT NULL, ville VARCHAR(255) DEFAULT NULL, pays VARCHAR(255) DEFAULT NULL, telephone VARCHAR(255) DEFAULT NULL, mobile VARCHAR(255) DEFAULT NULL, siret VARCHAR(255) DEFAULT NULL, nom_entreprise VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E6381E61B789 FOREIGN KEY (repertoire_id) REFERENCES repertoire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52B19EB6921 FOREIGN KEY (client_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE devis_ligne ADD CONSTRAINT FK_41D3C6A741DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE documents_utilisateur ADD CONSTRAINT FK_C7800232A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE documents_utilisateur ADD CONSTRAINT FK_C7800232611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE documents_utilisateur ADD CONSTRAINT FK_C78002328826AFA6 FOREIGN KEY (type_document_id) REFERENCES type_document (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dossier ADD CONSTRAINT FK_3D48E037A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dossier ADD CONSTRAINT FK_3D48E037AEF5A6C1 FOREIGN KEY (services_id) REFERENCES services (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574AAEF5A6C1 FOREIGN KEY (services_id) REFERENCES services (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE86641019EB6921 FOREIGN KEY (client_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE facture_ligne ADD CONSTRAINT FK_C5C453347F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FC33F7837 FOREIGN KEY (document_id) REFERENCES documents_utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1E7F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE repertoire ADD CONSTRAINT FK_3C367876A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE repertoire ADD CONSTRAINT FK_3C367876611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE services_user ADD CONSTRAINT FK_9AA8EF8AEF5A6C1 FOREIGN KEY (services_id) REFERENCES services (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE services_user ADD CONSTRAINT FK_9AA8EF8A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE type_document ADD CONSTRAINT FK_1596AD8AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE contact DROP CONSTRAINT FK_4C62E6381E61B789');
        $this->addSql('ALTER TABLE contact DROP CONSTRAINT FK_4C62E638A76ED395');
        $this->addSql('ALTER TABLE devis DROP CONSTRAINT FK_8B27C52B19EB6921');
        $this->addSql('ALTER TABLE devis_ligne DROP CONSTRAINT FK_41D3C6A741DEFADA');
        $this->addSql('ALTER TABLE documents_utilisateur DROP CONSTRAINT FK_C7800232A76ED395');
        $this->addSql('ALTER TABLE documents_utilisateur DROP CONSTRAINT FK_C7800232611C0C56');
        $this->addSql('ALTER TABLE documents_utilisateur DROP CONSTRAINT FK_C78002328826AFA6');
        $this->addSql('ALTER TABLE dossier DROP CONSTRAINT FK_3D48E037A76ED395');
        $this->addSql('ALTER TABLE dossier DROP CONSTRAINT FK_3D48E037AEF5A6C1');
        $this->addSql('ALTER TABLE events DROP CONSTRAINT FK_5387574AA76ED395');
        $this->addSql('ALTER TABLE events DROP CONSTRAINT FK_5387574AAEF5A6C1');
        $this->addSql('ALTER TABLE facture DROP CONSTRAINT FK_FE86641019EB6921');
        $this->addSql('ALTER TABLE facture_ligne DROP CONSTRAINT FK_C5C453347F2DEE08');
        $this->addSql('ALTER TABLE image DROP CONSTRAINT FK_C53D045FC33F7837');
        $this->addSql('ALTER TABLE paiement DROP CONSTRAINT FK_B1DC7A1E7F2DEE08');
        $this->addSql('ALTER TABLE repertoire DROP CONSTRAINT FK_3C367876A76ED395');
        $this->addSql('ALTER TABLE repertoire DROP CONSTRAINT FK_3C367876611C0C56');
        $this->addSql('ALTER TABLE services_user DROP CONSTRAINT FK_9AA8EF8AEF5A6C1');
        $this->addSql('ALTER TABLE services_user DROP CONSTRAINT FK_9AA8EF8A76ED395');
        $this->addSql('ALTER TABLE type_document DROP CONSTRAINT FK_1596AD8AA76ED395');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE devis');
        $this->addSql('DROP TABLE devis_ligne');
        $this->addSql('DROP TABLE devis_version');
        $this->addSql('DROP TABLE documents_utilisateur');
        $this->addSql('DROP TABLE dossier');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE facture_ligne');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE paiement');
        $this->addSql('DROP TABLE repertoire');
        $this->addSql('DROP TABLE services');
        $this->addSql('DROP TABLE services_user');
        $this->addSql('DROP TABLE type_document');
        $this->addSql('DROP TABLE "user"');
    }
}
