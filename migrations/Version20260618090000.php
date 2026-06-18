<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Rend view.user_id NOT NULL : une vue est toujours rattachée à un utilisateur
 * (ReadMangaController redirige vers la connexion si aucun utilisateur).
 */
final class Version20260618090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'view.user_id devient NOT NULL';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE view DROP FOREIGN KEY FK_FEFDAB8EA76ED395');
        $this->addSql('ALTER TABLE view CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE view ADD CONSTRAINT FK_FEFDAB8EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE view DROP FOREIGN KEY FK_FEFDAB8EA76ED395');
        $this->addSql('ALTER TABLE view CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE view ADD CONSTRAINT FK_FEFDAB8EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }
}
