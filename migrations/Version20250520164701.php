<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250520164701 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE lesson ADD student_id INT DEFAULT NULL, ADD teacher_id INT DEFAULT NULL, ADD employee_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lesson ADD CONSTRAINT FK_F87474F341807E1D FOREIGN KEY (teacher_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lesson ADD CONSTRAINT FK_F87474F38C03F15C FOREIGN KEY (employee_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F87474F3CB944F1A ON lesson (student_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F87474F341807E1D ON lesson (teacher_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F87474F38C03F15C ON lesson (employee_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3CB944F1A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F341807E1D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F38C03F15C
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_F87474F3CB944F1A ON lesson
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_F87474F341807E1D ON lesson
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_F87474F38C03F15C ON lesson
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lesson DROP student_id, DROP teacher_id, DROP employee_id
        SQL);
    }
}
