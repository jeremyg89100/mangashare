<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260625120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Suppression de la colonne morte manga.views (le comptage reel passe par la table view)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE manga DROP views');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE manga ADD views INT NOT NULL');
    }
}
