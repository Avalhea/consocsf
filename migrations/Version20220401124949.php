<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220401124949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649DA54E4D4 FOREIGN KEY (ud_id) REFERENCES ud (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649DA54E4D4 ON user (ud_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649DA54E4D4');
        $this->addSql('DROP INDEX UNIQ_8D93D649DA54E4D4 ON user');
    }
}
