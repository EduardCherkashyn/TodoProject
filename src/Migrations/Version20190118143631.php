<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190118143631 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE item ADD attachment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E464E68B FOREIGN KEY (attachment_id) REFERENCES attachment (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1F1B251E464E68B ON item (attachment_id)');
        $this->addSql('ALTER TABLE attachment DROP FOREIGN KEY FK_795FD9BB126F525E');
        $this->addSql('DROP INDEX UNIQ_795FD9BB126F525E ON attachment');
        $this->addSql('ALTER TABLE attachment DROP item_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE attachment ADD item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE attachment ADD CONSTRAINT FK_795FD9BB126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_795FD9BB126F525E ON attachment (item_id)');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E464E68B');
        $this->addSql('DROP INDEX UNIQ_1F1B251E464E68B ON item');
        $this->addSql('ALTER TABLE item DROP attachment_id');
    }
}
