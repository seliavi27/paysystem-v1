<?php

enum PaymentMethod: string
{
    case CREDIT_CARD = 'credit_card';
    case BANK_TRANSFER = 'bank_transfer';
    case DIGITAL_WALLET = 'digital_wallet';
    case CRYPTOCURRENCY = 'cryptocurrency';

    public function getLabel(): string
    {
        return match ($this)
        {
            self::CREDIT_CARD => 'Банковская карта',
            self::BANK_TRANSFER => 'Банковский перевод',
            self::DIGITAL_WALLET => 'Электронный кошелёк',
            self::CRYPTOCURRENCY => 'Криптовалюта',
        };
    }

    public function getCommission(): float
    {
        return match ($this)
        {
            self::CREDIT_CARD => 2.5,
            self::BANK_TRANSFER => 1.0,
            self::DIGITAL_WALLET => 0.5,
            self::CRYPTOCURRENCY => 3.0,
        };
    }

    public static function tryFromString(string $value): ?self
    {
        return self::tryFrom(strtoupper($value));
    }
}