<?php

declare(strict_types=1);

namespace PaySystem\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260427180724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'task-04: align users/payments schema with Doctrine ORM mapping (UUID -> VARCHAR(36), TIMESTAMPTZ -> TIMESTAMP, doctrine-style index names). Transactions table stays on DBAL — out of scope.';
    }

    public function up(Schema $schema): void
    {
        // drop FKs that block ALTER COLUMN TYPE (we'll recreate them after retyping)
        $this->addSql('ALTER TABLE transactions DROP CONSTRAINT IF EXISTS transactions_payment_id_fkey');
        $this->addSql('ALTER TABLE payments     DROP CONSTRAINT IF EXISTS payments_user_id_fkey');

        // sync FK column on transactions to VARCHAR(36) so it stays compatible with payments.id below
        $this->addSql('ALTER TABLE transactions ALTER payment_id TYPE VARCHAR(36)');
        $this->addSql('ALTER TABLE transactions ALTER id         TYPE VARCHAR(36)');

        $this->addSql('ALTER TABLE payments ALTER id TYPE VARCHAR(36)');
        $this->addSql('ALTER TABLE payments ALTER user_id TYPE VARCHAR(36)');
        $this->addSql('ALTER TABLE payments ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE payments ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER INDEX idx_payments_user_id RENAME TO IDX_65D29B32A76ED395');
        $this->addSql('ALTER INDEX idx_payments_user_id_status RENAME TO idx_payments_user_status');
        $this->addSql('ALTER TABLE users ALTER id TYPE VARCHAR(36)');
        $this->addSql('ALTER TABLE users ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE users ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER INDEX users_email_key RENAME TO UNIQ_1483A5E9E7927C74');

        // recreate FKs (Doctrine ORM mapping uses ON DELETE CASCADE for payments.user_id)
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT payments_user_id_fkey FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transactions ADD CONSTRAINT transactions_payment_id_fkey FOREIGN KEY (payment_id) REFERENCES payments (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payments ALTER created_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE payments ALTER updated_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE payments ALTER id TYPE UUID USING id::uuid');
        $this->addSql('ALTER TABLE payments ALTER user_id TYPE UUID USING user_id::uuid');
        $this->addSql('ALTER INDEX idx_payments_user_status RENAME TO idx_payments_user_id_status');
        $this->addSql('ALTER INDEX idx_65d29b32a76ed395 RENAME TO idx_payments_user_id');
        $this->addSql('ALTER TABLE users ALTER created_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE users ALTER updated_at TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE users ALTER id TYPE UUID USING id::uuid');
        $this->addSql('ALTER INDEX uniq_1483a5e9e7927c74 RENAME TO users_email_key');

        $this->addSql('ALTER TABLE transactions ALTER payment_id TYPE UUID USING payment_id::uuid');
        $this->addSql('ALTER TABLE transactions ALTER id         TYPE UUID USING id::uuid');
    }
}
