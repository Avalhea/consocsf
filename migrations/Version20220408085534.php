<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220408085534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681E6AB213CC');
        $this->addSql('DROP INDEX IDX_B26681E6AB213CC ON evenement');
        $this->addSql('ALTER TABLE evenement DROP lieu_id');
        $this->addSql('ALTER TABLE lieu ADD evenement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lieu ADD CONSTRAINT FK_2F577D59FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2F577D59FD02F13 ON lieu (evenement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement ADD lieu_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681E6AB213CC FOREIGN KEY (lieu_id) REFERENCES lieu (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_B26681E6AB213CC ON evenement (lieu_id)');
        $this->addSql('ALTER TABLE lieu DROP FOREIGN KEY FK_2F577D59FD02F13');
        $this->addSql('DROP INDEX UNIQ_2F577D59FD02F13 ON lieu');
        $this->addSql('ALTER TABLE lieu DROP evenement_id');
    }
}
