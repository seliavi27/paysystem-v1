<?php
declare(strict_types=1);

namespace App\Enum;

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