<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230424224628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD id_role_id INT DEFAULT NULL, CHANGE image image VARCHAR(255) NOT NULL, CHANGE reset_token reset_token VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64989E8BDC FOREIGN KEY (id_role_id) REFERENCES role (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64989E8BDC ON user (id_role_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64989E8BDC');
        $this->addSql('DROP INDEX IDX_8D93D64989E8BDC ON user');
        $this->addSql('ALTER TABLE user DROP id_role_id, CHANGE image image VARCHAR(255) DEFAULT NULL, CHANGE reset_token reset_token VARCHAR(255) DEFAULT NULL');
    }
}
