<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241126044443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mission_pouvoirs (mission_id INT NOT NULL, pouvoir_id INT NOT NULL, INDEX IDX_614BD659BE6CAE90 (mission_id), INDEX IDX_614BD659C8A705F8 (pouvoir_id), PRIMARY KEY(mission_id, pouvoir_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mission_pouvoirs ADD CONSTRAINT FK_614BD659BE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mission_pouvoirs ADD CONSTRAINT FK_614BD659C8A705F8 FOREIGN KEY (pouvoir_id) REFERENCES pouvoir (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mission_pouvoirs DROP FOREIGN KEY FK_614BD659BE6CAE90');
        $this->addSql('ALTER TABLE mission_pouvoirs DROP FOREIGN KEY FK_614BD659C8A705F8');
        $this->addSql('DROP TABLE mission_pouvoirs');
    }
}
