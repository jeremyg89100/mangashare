<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260611074004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE view (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, manga_id INT DEFAULT NULL, article_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_FEFDAB8E7B6461 (manga_id), INDEX IDX_FEFDAB8E7294869C (article_id), INDEX IDX_FEFDAB8EA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE view ADD CONSTRAINT FK_FEFDAB8E7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id)');
        $this->addSql('ALTER TABLE view ADD CONSTRAINT FK_FEFDAB8E7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE view ADD CONSTRAINT FK_FEFDAB8EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE like_article CHANGE user_id user_id INT NOT NULL, CHANGE article_id article_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE view DROP FOREIGN KEY FK_FEFDAB8E7B6461');
        $this->addSql('ALTER TABLE view DROP FOREIGN KEY FK_FEFDAB8E7294869C');
        $this->addSql('ALTER TABLE view DROP FOREIGN KEY FK_FEFDAB8EA76ED395');
        $this->addSql('DROP TABLE view');
        $this->addSql('ALTER TABLE like_article CHANGE user_id user_id INT DEFAULT NULL, CHANGE article_id article_id INT DEFAULT NULL');
    }
}
