<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220407101202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dossiers (id INT AUTO_INCREMENT NOT NULL, communications_electroniques INT DEFAULT NULL, banque INT DEFAULT NULL, surendettement INT DEFAULT NULL, assurance INT DEFAULT NULL, sante INT DEFAULT NULL, services INT DEFAULT NULL, energie INT DEFAULT NULL, eau INT DEFAULT NULL, automobile INT DEFAULT NULL, logement_loc_social INT DEFAULT NULL, logement_loc_priv INT DEFAULT NULL, logement_propri INT DEFAULT NULL, travaux INT DEFAULT NULL, pratiques_com_deloyales INT DEFAULT NULL, defense_droits_acces INT DEFAULT NULL, nb_autre INT DEFAULT NULL, autre_libelle VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE dossiers');
    }
}
