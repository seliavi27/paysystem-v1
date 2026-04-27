<?php

declare(strict_types=1);

namespace PaySystem\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Расширяет transactions под task-3 audit-функциональность:
 * добавляет user_id (FK на users), currency и description колонки.
 *
 * Миграция написана вручную — таблица transactions исключена из ORM-mapping
 * (см. setSchemaAssetsFilter в bootstrap.php), поэтому Doctrine diff её не видит.
 */
final class Version20260427183500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'task-3: extend transactions table with user_id (FK), currency, description for PaymentService audit trail.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE transactions ADD COLUMN IF NOT EXISTS user_id     VARCHAR(36)');
        $this->addSql('ALTER TABLE transactions ADD COLUMN IF NOT EXISTS currency    VARCHAR(3)');
        $this->addSql("ALTER TABLE transactions ADD COLUMN IF NOT EXISTS description TEXT NOT NULL DEFAULT ''");

        // backfill для существующих строк (если есть)
        $this->addSql('UPDATE transactions t SET user_id = p.user_id, currency = p.currency
                       FROM payments p WHERE p.id = t.payment_id AND t.user_id IS NULL');

        $this->addSql('ALTER TABLE transactions ALTER COLUMN user_id  SET NOT NULL');
        $this->addSql('ALTER TABLE transactions ALTER COLUMN currency SET NOT NULL');

        $this->addSql('ALTER TABLE transactions DROP CONSTRAINT IF EXISTS transactions_user_id_fkey');
        $this->addSql('ALTER TABLE transactions ADD  CONSTRAINT transactions_user_id_fkey
                         FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_transactions_user_id ON transactions(user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IF EXISTS idx_transactions_user_id');
        $this->addSql('ALTER TABLE transactions DROP CONSTRAINT IF EXISTS transactions_user_id_fkey');
        $this->addSql('ALTER TABLE transactions DROP COLUMN IF EXISTS description');
        $this->addSql('ALTER TABLE transactions DROP COLUMN IF EXISTS currency');
        $this->addSql('ALTER TABLE transactions DROP COLUMN IF EXISTS user_id');
    }
}
