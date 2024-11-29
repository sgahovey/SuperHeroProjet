<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241129102042 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mission DROP INDEX UNIQ_9067F23C937C8CB0, ADD INDEX IDX_9067F23C937C8CB0 (equipe_assignee_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mission DROP INDEX IDX_9067F23C937C8CB0, ADD UNIQUE INDEX UNIQ_9067F23C937C8CB0 (equipe_assignee_id)');
    }
}
