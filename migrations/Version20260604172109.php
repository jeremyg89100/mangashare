<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Aligne le schéma `like_article` sur le typage non-nullable de l'entité :
 * un like appartient toujours à un utilisateur et à un article.
 */
final class Version20260604172109 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'like_article: user_id et article_id passent en NOT NULL';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE like_article CHANGE user_id user_id INT NOT NULL, CHANGE article_id article_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE like_article CHANGE user_id user_id INT DEFAULT NULL, CHANGE article_id article_id INT DEFAULT NULL');
    }
}
