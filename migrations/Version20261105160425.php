<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261105160425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_sauce_room (user_id INT NOT NULL, sauce_room_id INT NOT NULL, PRIMARY KEY (user_id, sauce_room_id))');
        $this->addSql('CREATE INDEX IDX_FDC4841EA76ED395 ON user_sauce_room (user_id)');
        $this->addSql('CREATE INDEX IDX_FDC4841E5D91BD01 ON user_sauce_room (sauce_room_id)');
        $this->addSql('ALTER TABLE user_sauce_room ADD CONSTRAINT FK_FDC4841EA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_sauce_room ADD CONSTRAINT FK_FDC4841E5D91BD01 FOREIGN KEY (sauce_room_id) REFERENCES sauce_room (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX uniq_5aa342f273154ed4');
        $this->addSql('CREATE INDEX IDX_5AA342F273154ED4 ON sauce_room (leader_id)');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d6495d91bd01');
        $this->addSql('DROP INDEX idx_8d93d6495d91bd01');
        $this->addSql('ALTER TABLE "user" DROP sauce_room_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_sauce_room DROP CONSTRAINT FK_FDC4841EA76ED395');
        $this->addSql('ALTER TABLE user_sauce_room DROP CONSTRAINT FK_FDC4841E5D91BD01');
        $this->addSql('DROP TABLE user_sauce_room');
        $this->addSql('DROP INDEX IDX_5AA342F273154ED4');
        $this->addSql('CREATE UNIQUE INDEX uniq_5aa342f273154ed4 ON sauce_room (leader_id)');
        $this->addSql('ALTER TABLE "user" ADD sauce_room_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d6495d91bd01 FOREIGN KEY (sauce_room_id) REFERENCES sauce_room (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_8d93d6495d91bd01 ON "user" (sauce_room_id)');
    }
}
