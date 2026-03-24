<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260105100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add views, likes, dislikes to article and create comment table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article ADD views INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE article ADD likes INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE article ADD dislikes INT NOT NULL DEFAULT 0');

        $this->addSql('CREATE TABLE comment (
            id INT AUTO_INCREMENT NOT NULL,
            author_name VARCHAR(100) NOT NULL,
            author_email VARCHAR(180) DEFAULT NULL,
            content LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL,
            article_id INT NOT NULL,
            INDEX IDX_9474526C7294869C (article_id),
            PRIMARY KEY(id),
            CONSTRAINT FK_9474526C7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE comment');
        $this->addSql('ALTER TABLE article DROP views, DROP likes, DROP dislikes');
    }
}
