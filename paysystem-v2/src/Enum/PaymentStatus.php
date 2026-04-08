<?php
declare(strict_types=1);

namespace PaySystem\Enum;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    public function getLabel(): string
    {
        return match ($this)
        {
            self::PENDING => 'Ожидание',
            self::PROCESSING => 'Обработка',
            self::COMPLETED => 'Завершено',
            self::FAILED => 'Ошибка',
            self::REFUNDED => 'Возвращён',
        };
    }

    public static function tryFromString(string $value): ?self
    {
        return self::tryFrom(strtolower($value));
    }
}