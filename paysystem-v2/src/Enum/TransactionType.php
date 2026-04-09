<?php

enum TransactionType: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';
    case REFUND = 'refund';
    case COMMISSION = 'commission';

    public function getLabel(): string
    {
        return match ($this)
        {
            self::INCOME => 'Доход',
            self::EXPENSE => 'Расход',
            self::REFUND => 'Возврат',
            self::COMMISSION => 'Комиссия',
        };
    }

    public static function tryFromString(string $value): ?self
    {
        return self::tryFrom(strtoupper($value));
    }
}