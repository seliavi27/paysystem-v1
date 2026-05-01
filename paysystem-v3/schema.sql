-- schema.sql
CREATE TABLE IF NOT EXISTS users (
    id              UUID PRIMARY KEY,
    email           VARCHAR(255) NOT NULL UNIQUE,
    password        VARCHAR(255) NOT NULL,     -- bcrypt hash, совместим с User::hashPassword
    full_name       VARCHAR(255) NOT NULL,     -- V2: fullName
    phone           VARCHAR(32)  NOT NULL DEFAULT '',
    balance         NUMERIC(15,2) NOT NULL DEFAULT 0,
    created_at      TIMESTAMPTZ  NOT NULL,
    updated_at      TIMESTAMPTZ  NOT NULL
);

CREATE TABLE IF NOT EXISTS payments (
    id              UUID PRIMARY KEY,
    user_id         UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    amount          NUMERIC(15,2) NOT NULL,
    description     TEXT         NOT NULL DEFAULT '',
    currency        VARCHAR(3)   NOT NULL,             -- PaySystem\Enum\CurrencyType: RUB | USD | EUR
    status          VARCHAR(16)  NOT NULL,             -- PaymentStatus: pending|processing|completed|failed|refunded
    method          VARCHAR(32)  NOT NULL,             -- PaymentMethod: credit_card|bank_transfer|digital_wallet
    created_at      TIMESTAMPTZ  NOT NULL,
    updated_at      TIMESTAMPTZ  NOT NULL
);

-- transactions: расширены поля под Entity\Transaction (audit-record для процессинга платежа)
CREATE TABLE IF NOT EXISTS transactions (
    id              UUID PRIMARY KEY,
    payment_id      UUID NOT NULL REFERENCES payments(id) ON DELETE CASCADE,
    user_id         UUID NOT NULL REFERENCES users(id)    ON DELETE CASCADE,
    type            VARCHAR(16)  NOT NULL,             -- PaySystem\Enum\TransactionType
    currency        VARCHAR(3)   NOT NULL,             -- PaySystem\Enum\CurrencyType
    amount          NUMERIC(15,2) NOT NULL,
    description     TEXT         NOT NULL DEFAULT '',
    created_at      TIMESTAMPTZ  NOT NULL
);

-- Индексы под запросы V2
CREATE INDEX IF NOT EXISTS idx_payments_user_id         ON payments(user_id);
CREATE INDEX IF NOT EXISTS idx_payments_user_id_status  ON payments(user_id, status);
CREATE INDEX IF NOT EXISTS idx_payments_status          ON payments(status);
CREATE INDEX IF NOT EXISTS idx_transactions_payment_id  ON transactions(payment_id);
CREATE INDEX IF NOT EXISTS idx_transactions_user_id     ON transactions(user_id);
