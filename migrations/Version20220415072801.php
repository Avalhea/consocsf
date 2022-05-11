<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220415072801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action_justice DROP FOREIGN KEY FK_5DC6288E6AB213CC');
        $this->addSql('DROP INDEX IDX_5DC6288E6AB213CC ON action_justice');
        $this->addSql('ALTER TABLE action_justice DROP lieu_id');
        $this->addSql('ALTER TABLE lieu ADD action_justice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lieu ADD CONSTRAINT FK_2F577D59A2B03AC8 FOREIGN KEY (action_justice_id) REFERENCES action_justice (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2F577D59A2B03AC8 ON lieu (action_justice_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action_justice ADD lieu_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE action_justice ADD CONSTRAINT FK_5DC6288E6AB213CC FOREIGN KEY (lieu_id) REFERENCES lieu (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5DC6288E6AB213CC ON action_justice (lieu_id)');
        $this->addSql('ALTER TABLE lieu DROP FOREIGN KEY FK_2F577D59A2B03AC8');
        $this->addSql('DROP INDEX UNIQ_2F577D59A2B03AC8 ON lieu');
        $this->addSql('ALTER TABLE lieu DROP action_justice_id');
    }
}
