<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150324145911 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE settings DROP createdAt, DROP updatedAt');
        $this->addSql('ALTER TABLE timepasses DROP createdAt, DROP updatedAt');
        $this->addSql('ALTER TABLE categories DROP createdAt, DROP updatedAt');
        $this->addSql('ALTER TABLE posts DROP createdAt, DROP updatedAt');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE categories ADD createdAt DATETIME NOT NULL, ADD updatedAt DATETIME NOT NULL');
        $this->addSql('ALTER TABLE posts ADD createdAt DATETIME NOT NULL, ADD updatedAt DATETIME NOT NULL');
        $this->addSql('ALTER TABLE settings ADD createdAt DATETIME NOT NULL, ADD updatedAt DATETIME NOT NULL');
        $this->addSql('ALTER TABLE timepasses ADD createdAt DATETIME NOT NULL, ADD updatedAt DATETIME NOT NULL');
    }
}
