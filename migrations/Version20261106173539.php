<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261106173539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE question RENAME COLUMN discr TO type');
        $this->addSql('ALTER TABLE sauce_room DROP CONSTRAINT fk_5aa342f2a0f35d66');
        $this->addSql('ALTER TABLE sauce_room ADD CONSTRAINT FK_5AA342F2A0F35D66 FOREIGN KEY (current_question_id) REFERENCES question (id) ON DELETE SET NULL NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE question RENAME COLUMN type TO discr');
        $this->addSql('ALTER TABLE sauce_room DROP CONSTRAINT FK_5AA342F2A0F35D66');
        $this->addSql('ALTER TABLE sauce_room ADD CONSTRAINT fk_5aa342f2a0f35d66 FOREIGN KEY (current_question_id) REFERENCES question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
