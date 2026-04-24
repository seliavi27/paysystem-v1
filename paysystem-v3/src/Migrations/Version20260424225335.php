<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260424225335 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payments (amount NUMERIC(15, 2) NOT NULL, description TEXT DEFAULT \'\' NOT NULL, currency VARCHAR(3) NOT NULL, status VARCHAR(16) NOT NULL, method VARCHAR(32) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, id VARCHAR(36) NOT NULL, user_id VARCHAR(36) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_65D29B32A76ED395 ON payments (user_id)');
        $this->addSql('CREATE INDEX idx_payments_user_status ON payments (user_id, status)');
        $this->addSql('CREATE INDEX idx_payments_status ON payments (status)');
        $this->addSql('CREATE TABLE users (email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, full_name VARCHAR(255) NOT NULL, phone VARCHAR(32) DEFAULT \'\' NOT NULL, balance NUMERIC(15, 2) DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, id VARCHAR(36) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B32A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payments DROP CONSTRAINT FK_65D29B32A76ED395');
        $this->addSql('DROP TABLE payments');
        $this->addSql('DROP TABLE users');
    }
}
