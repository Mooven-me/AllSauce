<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261102075113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d6495d91bd01');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D6495D91BD01 FOREIGN KEY (sauce_room_id) REFERENCES sauce_room (id) ON DELETE SET NULL NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D6495D91BD01');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d6495d91bd01 FOREIGN KEY (sauce_room_id) REFERENCES sauce_room (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
