<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220211234545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE calendrier (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, entreprise VARCHAR(100) DEFAULT NULL, rue VARCHAR(200) NOT NULL, code_postal BIGINT NOT NULL, ville VARCHAR(150) NOT NULL, telephone BIGINT NOT NULL, email VARCHAR(200) NOT NULL, depart DATETIME NOT NULL, fin DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE calendrier');
        $this->addSql('ALTER TABLE clients CHANGE nom nom VARCHAR(100) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE prenom prenom VARCHAR(100) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE entreprise entreprise VARCHAR(200) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE rue rue VARCHAR(200) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE codepostal codepostal VARCHAR(20) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE ville ville VARCHAR(100) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE telephone telephone VARCHAR(20) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(100) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE contact CHANGE nom nom VARCHAR(100) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE prenom prenom VARCHAR(100) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE telephone telephone VARCHAR(20) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(100) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE message message VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE course CHANGE adresse_depart adresse_depart VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE adresse_arrivee adresse_arrivee VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
