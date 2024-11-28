<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241128074439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mission DROP FOREIGN KEY FK_9067F23C2AAABE5B');
        $this->addSql('DROP INDEX IDX_9067F23C2AAABE5B ON mission');
        $this->addSql('ALTER TABLE mission DROP equipe_historique_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mission ADD equipe_historique_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mission ADD CONSTRAINT FK_9067F23C2AAABE5B FOREIGN KEY (equipe_historique_id) REFERENCES equipe (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_9067F23C2AAABE5B ON mission (equipe_historique_id)');
    }
}
