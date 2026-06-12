<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260514120314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_23A0E66A76ED395 ON article (user_id)');
        $this->addSql('ALTER TABLE chapter ADD manga_id INT NOT NULL');
        $this->addSql('ALTER TABLE chapter ADD CONSTRAINT FK_F981B52E7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id)');
        $this->addSql('CREATE INDEX IDX_F981B52E7B6461 ON chapter (manga_id)');
        $this->addSql('ALTER TABLE comment ADD user_id INT NOT NULL, ADD manga_id INT DEFAULT NULL, ADD article_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('CREATE INDEX IDX_9474526CA76ED395 ON comment (user_id)');
        $this->addSql('CREATE INDEX IDX_9474526C7B6461 ON comment (manga_id)');
        $this->addSql('CREATE INDEX IDX_9474526C7294869C ON comment (article_id)');
        $this->addSql('ALTER TABLE follow ADD follower_id INT NOT NULL, ADD following_id INT NOT NULL');
        $this->addSql('ALTER TABLE follow ADD CONSTRAINT FK_68344470AC24F853 FOREIGN KEY (follower_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE follow ADD CONSTRAINT FK_683444701816E3A3 FOREIGN KEY (following_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_68344470AC24F853 ON follow (follower_id)');
        $this->addSql('CREATE INDEX IDX_683444701816E3A3 ON follow (following_id)');
        $this->addSql('ALTER TABLE `like` ADD manga_id INT NOT NULL');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B37B6461 FOREIGN KEY (manga_id) REFERENCES manga (id)');
        $this->addSql('CREATE INDEX IDX_AC6340B37B6461 ON `like` (manga_id)');
        $this->addSql('ALTER TABLE manga ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE manga ADD CONSTRAINT FK_765A9E03A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_765A9E03A76ED395 ON manga (user_id)');
        $this->addSql('ALTER TABLE notification ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAA76ED395 ON notification (user_id)');
        $this->addSql('ALTER TABLE page ADD chapter_id INT NOT NULL');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB620579F4768 FOREIGN KEY (chapter_id) REFERENCES chapter (id)');
        $this->addSql('CREATE INDEX IDX_140AB620579F4768 ON page (chapter_id)');
        $this->addSql('ALTER TABLE reporting ADD author_id INT NOT NULL, ADD target_id INT NOT NULL');
        $this->addSql('ALTER TABLE reporting ADD CONSTRAINT FK_BD7CFA9FF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reporting ADD CONSTRAINT FK_BD7CFA9F158E0B66 FOREIGN KEY (target_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BD7CFA9FF675F31B ON reporting (author_id)');
        $this->addSql('CREATE INDEX IDX_BD7CFA9F158E0B66 ON reporting (target_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66A76ED395');
        $this->addSql('DROP INDEX IDX_23A0E66A76ED395 ON article');
        $this->addSql('ALTER TABLE article DROP user_id');
        $this->addSql('ALTER TABLE chapter DROP FOREIGN KEY FK_F981B52E7B6461');
        $this->addSql('DROP INDEX IDX_F981B52E7B6461 ON chapter');
        $this->addSql('ALTER TABLE chapter DROP manga_id');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C7B6461');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C7294869C');
        $this->addSql('DROP INDEX IDX_9474526CA76ED395 ON comment');
        $this->addSql('DROP INDEX IDX_9474526C7B6461 ON comment');
        $this->addSql('DROP INDEX IDX_9474526C7294869C ON comment');
        $this->addSql('ALTER TABLE comment DROP user_id, DROP manga_id, DROP article_id');
        $this->addSql('ALTER TABLE follow DROP FOREIGN KEY FK_68344470AC24F853');
        $this->addSql('ALTER TABLE follow DROP FOREIGN KEY FK_683444701816E3A3');
        $this->addSql('DROP INDEX IDX_68344470AC24F853 ON follow');
        $this->addSql('DROP INDEX IDX_683444701816E3A3 ON follow');
        $this->addSql('ALTER TABLE follow DROP follower_id, DROP following_id');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B37B6461');
        $this->addSql('DROP INDEX IDX_AC6340B37B6461 ON `like`');
        $this->addSql('ALTER TABLE `like` DROP manga_id');
        $this->addSql('ALTER TABLE manga DROP FOREIGN KEY FK_765A9E03A76ED395');
        $this->addSql('DROP INDEX IDX_765A9E03A76ED395 ON manga');
        $this->addSql('ALTER TABLE manga DROP user_id');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('DROP INDEX IDX_BF5476CAA76ED395 ON notification');
        $this->addSql('ALTER TABLE notification DROP user_id');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB620579F4768');
        $this->addSql('DROP INDEX IDX_140AB620579F4768 ON page');
        $this->addSql('ALTER TABLE page DROP chapter_id');
        $this->addSql('ALTER TABLE reporting DROP FOREIGN KEY FK_BD7CFA9FF675F31B');
        $this->addSql('ALTER TABLE reporting DROP FOREIGN KEY FK_BD7CFA9F158E0B66');
        $this->addSql('DROP INDEX IDX_BD7CFA9FF675F31B ON reporting');
        $this->addSql('DROP INDEX IDX_BD7CFA9F158E0B66 ON reporting');
        $this->addSql('ALTER TABLE reporting DROP author_id, DROP target_id');
    }
}
