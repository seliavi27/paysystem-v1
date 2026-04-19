<?php
declare(strict_types=1);

namespace PaySystem\Enum;

enum CurrencyType: string
{
    case BYN = 'BYN';
    case RUB = 'RUB';
    case USD = 'USD';
    case EUR = 'EUR';

    public function getSymbol(): string
    {
        return match ($this) {
            self::BYN => 'Б',
            self::RUB => '₽',
            self::USD => '$',
            self::EUR => '€',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::BYN => 'Белорусский рубль',
            self::RUB => 'Российский рубль',
            self::USD => 'Доллар США',
            self::EUR => 'Евро',
        };
    }

    public static function tryFromString(string $value): ?self
    {
        return self::tryFrom(strtoupper($value));
    }
}