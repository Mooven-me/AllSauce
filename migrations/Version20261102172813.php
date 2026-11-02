<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261102172813 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sauce_room ADD sauce_id INT NOT NULL');
        $this->addSql('ALTER TABLE sauce_room ADD CONSTRAINT FK_5AA342F27AB984B7 FOREIGN KEY (sauce_id) REFERENCES sauce (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_5AA342F27AB984B7 ON sauce_room (sauce_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sauce_room DROP CONSTRAINT FK_5AA342F27AB984B7');
        $this->addSql('DROP INDEX IDX_5AA342F27AB984B7');
        $this->addSql('ALTER TABLE sauce_room DROP sauce_id');
    }
}
