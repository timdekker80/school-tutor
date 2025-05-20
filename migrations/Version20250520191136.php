<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250520191136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE sick_report (id INT AUTO_INCREMENT NOT NULL, teacher_id INT DEFAULT NULL, message LONGTEXT NOT NULL, date DATETIME NOT NULL, INDEX IDX_69AAE45141807E1D (teacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sick_report ADD CONSTRAINT FK_69AAE45141807E1D FOREIGN KEY (teacher_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE sick_report DROP FOREIGN KEY FK_69AAE45141807E1D
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE sick_report
        SQL);
    }
}
