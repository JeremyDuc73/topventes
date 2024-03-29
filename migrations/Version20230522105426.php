<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230522105426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE rate_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE rate (id INT NOT NULL, product_id INT NOT NULL, author_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DFEC3F394584665A ON rate (product_id)');
        $this->addSql('CREATE INDEX IDX_DFEC3F39F675F31B ON rate (author_id)');
        $this->addSql('ALTER TABLE rate ADD CONSTRAINT FK_DFEC3F394584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rate ADD CONSTRAINT FK_DFEC3F39F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE rate_id_seq CASCADE');
        $this->addSql('ALTER TABLE rate DROP CONSTRAINT FK_DFEC3F394584665A');
        $this->addSql('ALTER TABLE rate DROP CONSTRAINT FK_DFEC3F39F675F31B');
        $this->addSql('DROP TABLE rate');
    }
}
