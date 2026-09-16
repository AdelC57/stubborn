<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260911065440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660A143AB7B FOREIGN KEY (sweatshirt_id) REFERENCES sweatshirt (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660A143AB7B');
    }
}
