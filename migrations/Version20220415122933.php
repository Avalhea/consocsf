<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220415122933 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE action_justice (id INT AUTO_INCREMENT NOT NULL, nb_action_conjointe INT DEFAULT NULL, nb_accompagnement INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE atelier (id INT AUTO_INCREMENT NOT NULL, lieu_id INT DEFAULT NULL, theme_atelier VARCHAR(50) DEFAULT NULL, nb_seances INT DEFAULT NULL, nb_personnes_total INT DEFAULT NULL, INDEX IDX_E1BB18236AB213CC (lieu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie_rep (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE communication (id INT AUTO_INCREMENT NOT NULL, type_communication_id INT NOT NULL, lieu_id INT DEFAULT NULL, nombre INT DEFAULT NULL, sujets VARCHAR(200) DEFAULT NULL, INDEX IDX_F9AFB5EB4BB29FBD (type_communication_id), INDEX IDX_F9AFB5EB6AB213CC (lieu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dossier (id INT AUTO_INCREMENT NOT NULL, nb_dossiers INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE echelle (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, detail_evenement LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formations (id INT AUTO_INCREMENT NOT NULL, nb_formations_annee INT DEFAULT NULL, theme_formation_et_participants LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lieu (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, statut_id INT DEFAULT NULL, ud_id INT DEFAULT NULL, permanence_id INT DEFAULT NULL, evenement_id INT DEFAULT NULL, formations_id INT DEFAULT NULL, action_justice_id INT DEFAULT NULL, nom VARCHAR(100) NOT NULL, nb_salaries INT DEFAULT NULL, nb_benevole INT DEFAULT NULL, adresse VARCHAR(254) DEFAULT NULL, jours_et_horaires_ouverture LONGTEXT DEFAULT NULL, nb_consom_rens_tel INT DEFAULT NULL, INDEX IDX_2F577D59A76ED395 (user_id), INDEX IDX_2F577D59F6203804 (statut_id), INDEX IDX_2F577D59DA54E4D4 (ud_id), UNIQUE INDEX UNIQ_2F577D59A9457964 (permanence_id), UNIQUE INDEX UNIQ_2F577D59FD02F13 (evenement_id), UNIQUE INDEX UNIQ_2F577D593BF5B0C2 (formations_id), UNIQUE INDEX UNIQ_2F577D59A2B03AC8 (action_justice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permanence (id INT AUTO_INCREMENT NOT NULL, nb_jours INT DEFAULT NULL, nb_heures INT DEFAULT NULL, nb_dossier_simple INT DEFAULT NULL, nb_dossier_difficile INT DEFAULT NULL, nb_total_dossiers INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE representation (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, lieu_id INT DEFAULT NULL, frequence INT DEFAULT NULL, INDEX IDX_29D5499EBCF5E72D (categorie_id), INDEX IDX_29D5499E6AB213CC (lieu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statut (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_communication (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ud (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, echelle_id INT NOT NULL, ud_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649DD268C11 (echelle_id), UNIQUE INDEX UNIQ_8D93D649DA54E4D4 (ud_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE atelier ADD CONSTRAINT FK_E1BB18236AB213CC FOREIGN KEY (lieu_id) REFERENCES lieu (id)');
        $this->addSql('ALTER TABLE communication ADD CONSTRAINT FK_F9AFB5EB4BB29FBD FOREIGN KEY (type_communication_id) REFERENCES type_communication (id)');
        $this->addSql('ALTER TABLE communication ADD CONSTRAINT FK_F9AFB5EB6AB213CC FOREIGN KEY (lieu_id) REFERENCES lieu (id)');
        $this->addSql('ALTER TABLE lieu ADD CONSTRAINT FK_2F577D59A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE lieu ADD CONSTRAINT FK_2F577D59F6203804 FOREIGN KEY (statut_id) REFERENCES statut (id)');
        $this->addSql('ALTER TABLE lieu ADD CONSTRAINT FK_2F577D59DA54E4D4 FOREIGN KEY (ud_id) REFERENCES ud (id)');
        $this->addSql('ALTER TABLE lieu ADD CONSTRAINT FK_2F577D59A9457964 FOREIGN KEY (permanence_id) REFERENCES permanence (id)');
        $this->addSql('ALTER TABLE lieu ADD CONSTRAINT FK_2F577D59FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE lieu ADD CONSTRAINT FK_2F577D593BF5B0C2 FOREIGN KEY (formations_id) REFERENCES formations (id)');
        $this->addSql('ALTER TABLE lieu ADD CONSTRAINT FK_2F577D59A2B03AC8 FOREIGN KEY (action_justice_id) REFERENCES action_justice (id)');
        $this->addSql('ALTER TABLE representation ADD CONSTRAINT FK_29D5499EBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie_rep (id)');
        $this->addSql('ALTER TABLE representation ADD CONSTRAINT FK_29D5499E6AB213CC FOREIGN KEY (lieu_id) REFERENCES lieu (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649DD268C11 FOREIGN KEY (echelle_id) REFERENCES echelle (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649DA54E4D4 FOREIGN KEY (ud_id) REFERENCES ud (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lieu DROP FOREIGN KEY FK_2F577D59A2B03AC8');
        $this->addSql('ALTER TABLE representation DROP FOREIGN KEY FK_29D5499EBCF5E72D');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649DD268C11');
        $this->addSql('ALTER TABLE lieu DROP FOREIGN KEY FK_2F577D59FD02F13');
        $this->addSql('ALTER TABLE lieu DROP FOREIGN KEY FK_2F577D593BF5B0C2');
        $this->addSql('ALTER TABLE atelier DROP FOREIGN KEY FK_E1BB18236AB213CC');
        $this->addSql('ALTER TABLE communication DROP FOREIGN KEY FK_F9AFB5EB6AB213CC');
        $this->addSql('ALTER TABLE representation DROP FOREIGN KEY FK_29D5499E6AB213CC');
        $this->addSql('ALTER TABLE lieu DROP FOREIGN KEY FK_2F577D59A9457964');
        $this->addSql('ALTER TABLE lieu DROP FOREIGN KEY FK_2F577D59F6203804');
        $this->addSql('ALTER TABLE communication DROP FOREIGN KEY FK_F9AFB5EB4BB29FBD');
        $this->addSql('ALTER TABLE lieu DROP FOREIGN KEY FK_2F577D59DA54E4D4');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649DA54E4D4');
        $this->addSql('ALTER TABLE lieu DROP FOREIGN KEY FK_2F577D59A76ED395');
        $this->addSql('DROP TABLE action_justice');
        $this->addSql('DROP TABLE atelier');
        $this->addSql('DROP TABLE categorie_rep');
        $this->addSql('DROP TABLE communication');
        $this->addSql('DROP TABLE dossier');
        $this->addSql('DROP TABLE echelle');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE formations');
        $this->addSql('DROP TABLE lieu');
        $this->addSql('DROP TABLE permanence');
        $this->addSql('DROP TABLE representation');
        $this->addSql('DROP TABLE statut');
        $this->addSql('DROP TABLE type_communication');
        $this->addSql('DROP TABLE ud');
        $this->addSql('DROP TABLE user');
    }
}
